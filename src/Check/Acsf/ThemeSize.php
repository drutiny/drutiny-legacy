<?php

namespace SiteAudit\Check\Acsf;

use SiteAudit\Check\Check;
use SiteAudit\AuditResponse\AuditResponse;
use SiteAudit\Executor\DoesNotApplyException;

class ThemeSize extends Check {

  static public function getNamespace()
  {
    return 'acsf/themesize';
  }

  public function check()
  {
    $context = $this->context;
    $status = $context->drush->coreStatus('--format=json')->parseJson();
    $command = "du -ms {$status->root}/{$status->site}/themes/site/ || echo 'nope'";
    $output = (string) $context->remoteExecutor->execute($command);

    // The ACSF site can have no custom theme repo linked, in which case we
    // will see a "du: cannot access ... No such file or directory" error
    // response. For now, we force this command to not fail using an or
    // statement.
    if (preg_match('/^nope$/', $output)) {
      throw new DoesNotApplyException();
    }

    // Output from du here is an int followed by space, followed by the full
    // path. We only want the int (size in MB).
    preg_match('/^(\d+)\s+\/.*/', $output, $matches);
    if (!isset($matches[1])) {
      throw new \Exception('Invalid response from du');
    }

    $size_in_mb = (int) $matches[1];
    $max_size = (int) $this->getOption('max_size', 50);
    $warning_size = (int) $this->getOption('warning_size', 20);
    $this->setToken('max_size', $max_size);
    $this->setToken('warning_size', $warning_size);
    $this->setToken('value', $size_in_mb);

    if ($size_in_mb >= $max_size) {
      return AuditResponse::AUDIT_FAILURE;
    }
    if ($size_in_mb >= $warning_size) {
      return AuditResponse::AUDIT_WARNING;
    }

    return AuditResponse::AUDIT_SUCCESS;
  }
}
