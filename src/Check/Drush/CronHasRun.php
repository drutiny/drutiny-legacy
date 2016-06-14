<?php

namespace SiteAudit\Check\Drush;

use SiteAudit\Check\Check;

class CronHasRun extends Check {

  protected function getNamespace()
  {
    return 'system/cron_has_run';
  }

  public function check()
  {
    if (!$cron_last = $this->context->drush->getVariable('cron_last')) {
      throw new \Exception("Cron has not run on the site.");
    }

    // For accurate math, we need to set the timezone correctly.
    if (!$date_default_timezone = $this->context->drush->getVariable('date_default_timezone')) {
      $date_default_timezone = 'UTC';
    }
    date_default_timezone_set($date_default_timezone);

    // Check that cron was run in the last day.
    $since = time() - $cron_last;
    $this->setToken('cron_last', date('Y-m-d H:i:s', $cron_last));
    if ($since > (86400)) {
      return FALSE;
    }

    return TRUE;
  }
}
