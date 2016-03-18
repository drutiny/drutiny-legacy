<?php

namespace SiteAudit\Check\Acsf;

use SiteAudit\Base\Check;
use SiteAudit\AuditResponse\AuditResponse;

class ThemeSize extends Check {
  public function check() {
    $response = new AuditResponse('acsf/themesize', $this);

    $response->test(function ($check) {
      $context = $check->context;
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
      $check->setToken('max_size', $max_size);
      $check->setToken('value', $size_in_mb);

      return $size_in_mb < $max_size;
    });

    return $response;
  }
}
