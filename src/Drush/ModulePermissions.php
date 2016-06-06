<?php

namespace SiteAudit\Drush;

use SiteAudit\Base\Check;
use SiteAudit\AuditResponse\AuditResponse;

class ModulePermissions extends Check {
  public function check() {
    $response = new AuditResponse('variable/module_permissions', $this);

    $response->test(function ($check) {
      $context = $check->context;
      $value = $context->drush->moduleEnabled('module_permissions');
      //If Module Permissions enabled check only admin has permission to list of modules
      if ($value) {
        $govcms_roles = $context->drush->getAllRoles();
        //remove the permission from all roles
        foreach ($govcms_roles as $role ) {
          $temp = $context->drush->roleRemovePerm("'".$role['role']."'", "'administer module permissions'");
        }
        //make sure admin role has the permission
        $temp = $context->drush->roleAddPerm("'administrator'", "'administer module permissions'");
        return TRUE;
      }

      return FALSE;
    });

    return $response;
  }
}


