<?php

namespace SiteAudit\Command;

use SiteAudit\Base\CoreStatus;
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

    // Ensure we can bootstrap the drush alias, and get the required properties
    // from the alias. Store these in an object to use in the checks to avoid
    // duplication.
    $core_status = new CoreStatus($drush_alias, $input, $output);

    // Profiles allow arbitrary checks to run in an arbitrary order. Optional
    // options can be passed in to customise the checks.
    $yaml = dirname(__FILE__) . "/../../profiles/${profile}.yml";
    if (!file_exists($yaml)) {
      throw new \Exception('missing profile YAML');
    }
    $parser = new Parser();
    $profile = $parser->parse(file_get_contents($yaml));
    foreach ($profile['checks'] as $check => $options) {
      $test = new $check($drush_alias, $input, $output, $options, $core_status);
      $test->check();
    }
  }

}
