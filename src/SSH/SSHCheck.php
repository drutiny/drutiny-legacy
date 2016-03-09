<?php

namespace SiteAudit\SSH;

use SiteAudit\Base\AuditCheck;
use Symfony\Component\Console\Output\OutputInterface;

abstract class SSHCheck extends AuditCheck {

  /**
   * Generate a full SSH connection string to connect to a webserver and run a
   * command.
   *
   * @param $command
   * @return string
   */
  private function generateSSHString($command) {
    return sprintf("ssh %s -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no -o LogLevel=ERROR %s@%s \"%s\"",
      $this->ssh_options,
      $this->remote_user,
      $this->primary_web,
      $command
    );
  }

  private function generateCid($command) {
    return substr(md5($command), 0, 8);
  }

  public function executeSSHCommand($command) {
    $command = $this->generateSSHString($command);
    $cid = $this->generateCid($command);
//    $cache_filepath = '/tmp/audit-' . $cid . '.out';
//    if (is_file($cache_filepath) && is_readable($cache_filepath)) {
//      if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
//        $this->output->writeln("<info>SSH command [cid: ${cid} CACHE HIT]</info>");
//        $this->output->writeln($command);
//      }
//      return unserialize(file_get_contents($cache_filepath));
//    }
    if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
      $this->output->writeln("<info>SSH command [cid: ${cid}] CACHE MISS</info>");
      $this->output->writeln($command);
    }

    $output = [];
    $return_var = 0;

    exec($command, $output, $return_var);

    if ($return_var !== 0) {
      throw new \Exception("SSH Command Failed ($return_var): " . print_r($output, 1));
    }

    // unwrap output if there is only a single line.
    if (count($output) === 1) {
      $output = current($output);
    }

    // Cache the output for later.
    //file_put_contents($cache_filepath, serialize($output));

    return $output;
  }
}
