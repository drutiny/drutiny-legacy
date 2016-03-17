<?php

namespace SiteAudit\Check;

use SiteAudit\Base\Check;
use SiteAudit\AuditResponse\AuditResponse;

class ModuleDisabled extends Check {
  public function check() {
    $response = new AuditResponse('module/disabled', $this);

    $response->test(function ($check) {

      $modules = $check->getOption('modules');
      $check->setToken('modules', implode(', ', $modules));

      $enabled = [];
      foreach ($modules as $module_name) {
        try {
          if ($check->context->drush->moduleEnabled($module_name)) {
            throw new \Exception($module_name);
          }
        }
        catch (\Exception $e) {
          $enabled[] = $module_name;
        }
      }

      if (!empty($enabled)) {
        $this->setToken('enabled', implode(', ', $enabled));
        return FALSE;
      }

      return TRUE;
    });

    return $response;
  }
}
