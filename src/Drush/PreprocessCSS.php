<?php

namespace SiteAudit\Drush;

use SiteAudit\Base\Check;
use SiteAudit\Base\AuditResponse;
use SiteAudit\Executor\ResultException;
use Symfony\Component\Console\Output\OutputInterface;

class PreprocessCss extends Check {
  public function check() {
    $response = new AuditResponse();
    $response->setDescription('With CSS optimization disabled, your website visitors are experiencing slower page performance and the server load is increased.');
    $response->setRemediation("Enable CSS optimization on Drupal's Performance page");

    try {
      $json = $this->context->drush->variableGet('preprocess_css', '--exact --format=json')->parseJson(TRUE);
      $output = (int) $json;
      if ($output === 1) {
        $response->setSuccess('CSS aggregation is enabled');
      }
      else {
        $response->setFailure('CSS aggregation is not enabled');
      }
    }
    catch (ResultException $e) {
      $response->setFailure("Could not determine CSS aggregation setting");
      if ($this->context->output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
        $this->context->output->writeln('<error>' . $e->getMessage() . '</error>');
      }
    }

    return $response;
  }
}
