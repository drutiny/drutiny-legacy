<?php

namespace SiteAudit\Command;

use SiteAudit\Base\CoreStatus;
use SiteAudit\Base\DrushCaller;
use SiteAudit\Base\Context;
use SiteAudit\Executor\Executor;
use SiteAudit\Executor\ExecutorRemote;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Console\Exception\InvalidArgumentException;

class SiteAudit extends Command {

  /**
   * @inheritdoc
   */
  protected function configure() {
    $this
      ->setName('audit:site')
      ->setDescription('Audit a Drupal site to ensure it meets best practice')
      ->addOption(
        'profile',
        null,
        InputOption::VALUE_REQUIRED,
        'What site audit profile do you want to use?',
        'default'
      )
      ->addOption(
        'ssh_options',
        null,
        InputOption::VALUE_OPTIONAL,
        'Passthrough any SSH options directly to SSH.',
        ''
      )
      ->addArgument(
        'drush-alias',
        InputArgument::REQUIRED,
        'The drush alias for the site'
      )
    ;
  }

  /**
   * @inheritdoc
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $drush_alias = $input->getArgument('drush-alias');

    $executor = new Executor($output);

    // Load the Drush alias which will contain more information we'll need.
    $drush = new DrushCaller($executor);
    $response = $drush->siteAlias('@' . $drush_alias, '--format=json')->parseJson(TRUE);
    $alias = $response[$drush_alias];
    $drush->setAlias($drush_alias);

    $profile = $this->loadProfile($input->getOption('profile'));

    $context = new Context();
    $context->set('input', $input)
            ->set('output', $output)
            ->set('profile', $profile)
            ->set('executor', $executor)
            ->set('remoteExecutor', $executor)
            ->set('drush', $drush)
            ->set('alias', $drush_alias)
            ->set('config', $alias);

    // Some checks don't use drush and connect to the server
    // directly so we need a remote executor available as well.
    if (isset($alias['remote-host'], $alias['remote-user'])) {
      $executor = new ExecutorRemote($output);
      $executor->setRemoteUser($alias['remote-user'])
               ->setRemoteHost($alias['remote-host']);
      if (isset($alias['ssh-options'])) {
        $executor->setArgument($alias['ssh-options']);
      }
      try {
        $executor->setArgument($input->getOption('ssh_options'));
      }
      catch (InvalidArgumentException $e) {}
      $context->set('remoteExecutor', $executor);
    }

    $this->runChecks($context);
  }

  protected function runChecks($context) {
    foreach ($context->profile['checks'] as $check => $options) {
      $test = new $check($context, $options);
      $context->output->writeln((string) $test->check());
    }
  }


  protected function loadProfile($profile) {
    // Profiles allow arbitrary checks to run in an arbitrary order. Optional
    // options can be passed in to customise the checks.
    $yaml = dirname(__FILE__) . "/../../profiles/${profile}.yml";
    if (!file_exists($yaml)) {
      throw new \Exception('missing profile YAML');
    }
    $parser = new Parser();
    $profile = $parser->parse(file_get_contents($yaml));
    return $profile;
  }

}
