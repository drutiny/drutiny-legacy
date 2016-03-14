<?php

namespace SiteAudit\Check\Acsf;

use SiteAudit\Base\Check;
use SiteAudit\AuditResponse\AuditResponse;

class ThemeSize extends Check {
  public function check() {
    $response = new AuditResponse('acsf/themesize');
    $context = $this->context;
    $cache = $this->getOption('cache', 300);
    $response->test(function () use ($context, $cache) {
      $status = $context->drush->coreStatus('--format=json')->parseJson();
      $command = "du -ms {$status->root}/{$status->site}/themes/site/";
      $output = (string) $context->remoteExecutor->execute($command);

      // Output from du here is an int followed by space, followed by the full
      // path. We only want the int (size in MB).
      preg_match('/^(\d+)\s+\/.*/', $output, $matches);
      if (!isset($matches[1])) {
        throw new \Exception('Invalid response from du');
      }
      $size_in_mb = (int) $matches[1];

      $max_size = (int) $this->getOption('max_size', 50);

      return $size_in_mb < $max_size;
    });

    return $response;
  }
}
