<?php

namespace SiteAudit\Drush;

use SiteAudit\Base\Check;
use SiteAudit\Base\AuditResponse;
use SiteAudit\Executor\ResultException;
use Symfony\Component\Console\Output\OutputInterface;

class PreprocessJS extends Check {
  public function check() {

    $response = new AuditResponse();
    $response->setDescription('With JavaScript file aggregation disabled, your website visitors are experiencing slower page loads and the server load is increased.');
    $response->setRemediation("Enable JS optimization on Drupal's Performance page");

    try {
      $json = $this->context->drush->variableGet('preprocess_js', '--exact --format=json')->parseJson(TRUE);
      $output = (int) $json;
      if ($output === 1) {
        $response->setSuccess('JavaScript aggregation is enabled');
      }
      else {
        $response->setFailure('JavaScript aggregation is not enabled');
      }
    }
    catch (ResultException $e) {
      $response->setFailure("Could not determine JS aggregation setting");
      if ($this->context->output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
        $this->context->output->writeln('<error>' . $e->getMessage() . '</error>');
      }
    }

    return $response;
  }
}
