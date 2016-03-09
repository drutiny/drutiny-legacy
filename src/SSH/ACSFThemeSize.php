<?php

namespace SiteAudit\SSH;

use SiteAudit\Base\AuditResponse;

class ACSFThemeSize extends SSHCheck {
  public function check() {
    $command = "du -ms {$this->root}/{$this->site}/themes/site/";
    $output = $this->executeSSHCommand($command);

    // Output from du here is an int followed by space, followed by the full
    // path. We only want the int (size in MB).
    preg_match('/^(\d+)\s+\/.*/', $output, $matches);
    if (!isset($matches[1])) {
      throw new \Exception('Invalid response from du');
    }
    $size_in_mb = (int) $matches[1];

    $max_size = 50;
    if (isset($this->options['max_size'])) {
      $max_size = (int) $this->options['max_size'];
    }

    $response = new AuditResponse();
    $response->setDescription('In Acquia Cloud Site Factory, you should aim to keep your theme repositories as small as possible to avoid exhausting the disk. Having theme repositories less than 50 MB is recommended.');
    $response->setRemediation("Reduce the number of unneeded files, e.g. PDFs, and PSDs of any initial designs, any node modules SaaS cache files.");
    if ($size_in_mb < $max_size) {
      $response->setSuccess("Theme folder less than ${max_size} MB, actual size ${size_in_mb} MB");
    }
    else {
      $response->setFailure("Theme folder greater than ${max_size} MB, actual size ${size_in_mb} MB");
    }

    $this->output->writeln((string) $response);
  }
}
