<?php

namespace SiteAudit\Check\Drush;

use SiteAudit\Check\Check;

class UpdateDBStatus extends Check {
  protected function getNamespace()
  {
    return 'system/updatedb';
  }
  public function check()
  {
    $context = $this->context;
    $output = $context->drush->updatedbStatus()->getOutput();
    // Sometimes "No database updates required" is in Stderr, and thus is
    // empty.
    if (empty($output)) {
      return TRUE;
    }
    if (count($output) === 1) {
      $output = reset($output);
      if (strpos($output, 'No database updates required') === 0) {
        return TRUE;
      }
    }

    return FALSE;
  }
}
