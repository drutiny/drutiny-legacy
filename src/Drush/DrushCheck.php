<?php

namespace SiteAudit\Drush;

use SiteAudit\Base\AuditCheck;
use Symfony\Component\Console\Output\OutputInterface;

abstract class DrushCheck extends AuditCheck {

  const DRUSH_BIN = 'drush';

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
      throw new \Exception('Non-zero response after running command');
    }

    // unwrap output if there is only a single line.
    if (count($output) === 1) {
      return current($output);
    }

    return $output;
  }
}
