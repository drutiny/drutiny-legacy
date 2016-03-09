<?php

namespace SiteAudit\Base;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AuditCheck {

  const DRUSH_BIN = 'drush';

  protected $alias;
  protected $input;
  protected $output;
  protected $options;
  protected $core_status;
  protected $uri;
  protected $primary_web;
  protected $remote_user;
  protected $ssh_options;
  protected $root;
  protected $site;

  /**
   * AuditCheck constructor.
   *
   * @param $alias
   * @param \Symfony\Component\Console\Input\InputInterface $input
   * @param \Symfony\Component\Console\Output\OutputInterface $output
   * @param array $options
   * @param \SiteAudit\Base\CoreStatus $core_status
   */
  public function __construct($alias, InputInterface $input, OutputInterface $output, $options = [], CoreStatus $core_status) {
    $this->alias = $alias;
    $this->input = $input;
    $this->output = $output;
    $this->options = $options;
    $this->core_status = $core_status;

    // Set some variables up.
    $this->uri = $this->core_status->getUri();
    $this->root = $this->core_status->getRoot();
    $this->site = $this->core_status->getSite();
    $this->ssh_options = $this->core_status->getSshOptions();
    $this->primary_web = $this->core_status->getPrimaryWeb();
    $this->remote_user = $this->core_status->getRemoteUser();
  }

  private function generateDrushString($command, $arguments, $options) {
    $arguments_string = '';
    $options_string = '';
    foreach ($arguments as $key => $value) {
      if (is_numeric($key)) {
        $arguments_string .= ' ' . $value;
      }
      else {
        $arguments_string .= ' --' . $key . '=' . $value;
      }
    }
    foreach ($options as $key => $value) {
      $options_string .= ' --' . $value;
    }
    return self::DRUSH_BIN . ' @' . $this->alias . ' --nocolor -y ' . $command . $arguments_string . $options_string;
  }

  /**
   * @param $command
   * @param array $arguments
   * @param array $options
   * @return string
   */
  public function executeDrush($command, $arguments = [], $options = []) {
    $command = $this->generateDrushString($command, $arguments, $options);

    if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
      $this->output->writeln('<info>Drush command</info>');
      $this->output->writeln($command);
    }

    $output = [];
    $return_var = 0;

    exec($command, $output, $return_var);

    if ($return_var !== 0) {
      throw new \Exception("Drush command failed ($return_var): " . print_r($output, 1));
    }

    // Datetime weirdness.
    if (strpos($output[0], 'date_timezone_set() expects parameter') === 0) {
      array_shift($output);
    }
    if (strpos($output[0], 'date_format() expects parameter') === 0) {
      array_shift($output);
    }

    // unwrap output if there is only a single line.
    if (count($output) === 1) {
      $output = current($output);

      // decode JSON.
      if (isset($arguments['format']) && $arguments['format'] === 'json') {
        $output = json_decode($output);
      }
    }

    if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE) {
      var_dump($output);
    }

    return $output;
  }

  /**
   * Try to find out if a module is enabled or not.
   *
   * @param $name
   * @return bool
   *   TRUE if the module is enabled, FALSE otherwise (including if the module
   *   was not found).
   */
  public function getModuleStatus($name) {
    try {
      $response = $this->executeDrush("pm-info ${name}", ['format' => 'json']);
      return ! $response->{$name}->status === "not installed";
    }
    // The response from Drush can be "No matching variable found.", even with
    // JSON being requested, which is weird.
    catch (\Exception $e) {
      return FALSE;
    }
  }

  /**
   * Get a single variable.
   *
   * @param $name
   * @return mixed
   * @throws \Exception
   */
  public function getVariable($name, $default = FALSE) {
    try {
      $response = $this->executeDrush("variable-get ${name}", ['format' => 'json'], ['exact']);
      return $response->$name;
    }
    // The response from Drush can be "No matching variable found.", even with
    // JSON being requested, which is weird.
    catch (\Exception $e) {
      return $default;
    }
  }

}
