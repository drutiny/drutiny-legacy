<?php

namespace SiteAudit\Drush;

use SiteAudit\Base\Check;
use SiteAudit\Base\AuditResponse;
use SiteAudit\Executor\ResultException;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateDBStatus extends Check {
  public function check() {
    $response = new AuditResponse();
    $response->setDescription("Updates to Drupal core or contrib modules sometimes include important database changes which should be applied after the code updates have been deployed.");
    $response->setRemediation("Required database updates should be applied by running `drush updatedb`");

    try {
      $output = $this->context->drush->updatedbStatus()->getOutput();
      if (!count($output)) {
        $response->setSuccess('No database updates required');
      }
      else {
        // Remove empty rows.
        $output = array_filter($output);

        // The header row can be discarded.
        $count = count($output);
        $response->setFailure("${count} database updates required.");
      }
    }
    catch (ResultException $e) {
      $response->setFailure("Could not determine DB status");
      if ($this->context->output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
        $this->context->output->writeln('<error>' . $e->getMessage() . '</error>');
      }
    }

    return $response;
  }
}
