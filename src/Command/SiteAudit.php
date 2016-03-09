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
    $profile = $input->getOption('profile');

    $executor = new Executor($output);

    // Load the Drush alias which will contain more information we'll need.
    $drush = new DrushCaller($executor);
    $response = $drush->siteAlias('@' . $drush_alias, '--format=json')->parseJson(TRUE);
    $alias = $response[$drush_alias];
    $drush->setAlias($drush_alias);

    $context = new Context();
    $context->set('input', $input)
            ->set('output', $output)
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
      if ($input->getOption('ssh_options')) {
        $executor->setArgument($input->getOption('ssh_options'));
      }
      $context->set('remoteExecutor', $executor);
    }

    // Ensure we can bootstrap the drush alias, and get the required properties
    // from the alias. Store these in an object to use in the checks to avoid
    // duplication.
    // $core_status = new CoreStatus($drush_alias, $input, $output);

    // Profiles allow arbitrary checks to run in an arbitrary order. Optional
    // options can be passed in to customise the checks.
    $yaml = dirname(__FILE__) . "/../../profiles/${profile}.yml";
    if (!file_exists($yaml)) {
      throw new \Exception('missing profile YAML');
    }
    $parser = new Parser();
    $profile = $parser->parse(file_get_contents($yaml));
    foreach ($profile['checks'] as $check => $options) {
      //$test = new $check($drush_alias, $input, $output, $options, $core_status);
      $test = new $check($context, $options);
      $output->writeln((string) $test->check());
    }
  }

}
