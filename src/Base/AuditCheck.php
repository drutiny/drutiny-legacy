<?php

namespace SiteAudit\Base;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AuditCheck {

  protected $alias;
  protected $input;
  protected $output;
  protected $options;
  protected $url;
  protected $primary_web;
  protected $docroot;
  protected $multisite_folder;

  public function __construct($alias, InputInterface $input, OutputInterface $output, $options = []) {
    $this->alias = $alias;
    $this->input = $input;
    $this->output = $output;
    $this->options = $options;
  }

}
