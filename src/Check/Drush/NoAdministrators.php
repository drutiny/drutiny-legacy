<?php

namespace SiteAudit\Check\Drush;

use SiteAudit\Check\Check;
use SiteAudit\AuditResponse\AuditResponse;
use SiteAudit\Annotation\CheckInfo;

/**
 * @CheckInfo(
 * title = "No Administrators",
 * description = "There should be no administrators other than user 1.",
 * remediation = "Check that only no users have the 'administrator' role, other than user 1.",
 * success = "No administrators other than user 1",
 * failure = "Currently there are <code>:value</code> administrators.",
 * not_available = "Could not determine administrator role.",
 * )
 */
class NoAdministrators extends Check {
  public function check()
  {
    $admin_role = $this->context->drush->getVariable('user_admin_role', 0);
    if (isset($admin_role)) {
      $user_roles = $this->context->drush->getAllUserRoles();
      $admin_count = 0;
      foreach ($user_roles as $role) {
        if (is_numeric($role) && (int) $admin_role === (int) $role) {
          $admin_count++;
        }
      }
      if ($admin_count > 0) {
        $this->setToken('value', $admin_count);
        return AuditResponse::AUDIT_FAILURE;
      }
    }
    else {
      return AuditResponse::AUDIT_NA;
    }

    return AuditResponse::AUDIT_SUCCESS;
  }
}
