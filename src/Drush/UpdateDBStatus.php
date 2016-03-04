<?php

namespace SiteAudit\Drush;

use SiteAudit\Base\AuditResponse;

class UpdateDBStatus extends DrushCheck {
  public function check() {
    $output = $this->executeDrush('updatedb-status');

    $response = new AuditResponse();
    $response->setDescription("Updates to Drupal core or contrib modules sometimes include important database changes which should be applied after the code updates have been deployed.");
    $response->setRemediation("Required database updates should be applied by running `drush updatedb`");
    if (is_string($output) && strpos($output, "No database updates required") === 0) {
      $response->setSuccess('No database updates required');
    }
    else {
      // Remove empty rows.
      $output = array_filter($output);

      // The header row can be discarded.
      $count = count($output) - 1;
      $response->setFailure("${count} database updates required.");
    }

    $this->output->writeln((string) $response);
  }
}
