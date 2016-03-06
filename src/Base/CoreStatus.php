<?php

namespace SiteAudit\Base;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CoreStatus {

  const DRUSH_BIN = 'drush';

  protected $alias;
  protected $input;
  protected $output;
  protected $core_status;
  protected $uri;
  protected $primary_web;
  protected $root;
  protected $site;

  public function __construct($alias, InputInterface $input, OutputInterface $output) {
    $this->alias = $alias;
    $this->input = $input;
    $this->output = $output;

    // Set some variables up.
    $this->core_status = $this->fetchCoreStatus();
    $this->uri = $this->core_status->uri;
    $this->root = $this->core_status->root;
    $this->site = $this->core_status->site;
    $this->primary_web = $this->fetchPrimaryWeb();
  }

  private function fetchCoreStatus() {
    $command = self::DRUSH_BIN . ' @' . $this->alias . ' core-status --format=json';

    if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
      $this->output->writeln('<info>Drush command</info>');
      $this->output->writeln($command);
    }

    $output = [];
    $return_var = 0;

    exec($command, $output, $return_var);

    if ($return_var !== 0) {
      throw new \Exception('Non-zero response after running command');
    }

    // unwrap output if there is only a single line.
    if (count($output) === 1) {
      $output = json_decode(current($output));
      return $output;
    }

    throw new \Exception('invalid response');
  }

  private function fetchPrimaryWeb() {
    $command = self::DRUSH_BIN . ' site-alias @' . $this->alias . ' --format=json';

    if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
      $this->output->writeln('<info>Drush command</info>');
      $this->output->writeln($command);
    }

    $output = [];
    $return_var = 0;

    exec($command, $output, $return_var);

    if ($return_var !== 0) {
      throw new \Exception('Non-zero response after running command');
    }

    // unwrap output if there is only a single line.
    if (count($output) === 1) {
      $output = json_decode(current($output));
      return $output->{$this->alias}->{'remote-host'};
    }

    throw new \Exception('invalid response');
  }

  /**
   * @return mixed
   */
  public function getPrimaryWeb() {
    return $this->primary_web;
  }

  /**
   * @return mixed
   */
  public function getUri() {
    return $this->uri;
  }

  /**
   * @return mixed
   */
  public function getRoot() {
    return $this->root;
  }

  /**
   * @return mixed
   */
  public function getSite() {
    return $this->site;
  }

}
