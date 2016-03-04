<?php

namespace SiteAudit\Command;

use SiteAudit\Drush\ModulePHP;
use SiteAudit\Drush\PreprocessCSS;
use SiteAudit\Drush\PreprocessJS;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SaasSiteAudit extends Command {
  protected function configure() {
    $this
      ->setName('audit:saas')
      ->setDescription('Audit a govCMS SaaS site to ensure it meets best practice')
      ->addArgument(
        'drush-alias',
        InputArgument::REQUIRED,
        'The drush alias for the site'
      )
      ->addArgument(
        'url',
        InputArgument::OPTIONAL,
        'The url to the site, e.g. www.govcms.com.au'
      )
    ;
  }

  protected function execute(InputInterface $input, OutputInterface $output) {
    $drush_alias = $input->getArgument('drush-alias');

    // @todo converstion to profiles using YAML so that each site can have it's
    // own tests in it's own order.

    $test = new PreprocessCSS($drush_alias, $input, $output);
    $test->check();

    $test = new PreprocessJS($drush_alias, $input, $output);
    $test->check();

    $test = new ModulePHP($drush_alias, $input, $output);
    $test->check();
  }
}
