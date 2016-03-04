<?php

namespace SiteAudit\Base;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AuditCheck {

  protected $alias;
  protected $input;
  protected $output;
  protected $url;
  protected $primary_web;
  protected $docroot;
  protected $multisite_folder;

  public function __construct($alias, InputInterface $input, OutputInterface $output) {
    $this->alias = $alias;
    $this->input = $input;
    $this->output = $output;

    // Figure out some other details based on the current drush alias.

  }



}
