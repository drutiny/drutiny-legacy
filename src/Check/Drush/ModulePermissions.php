<?php

namespace SiteAudit\Check\Drush;

use SiteAudit\Check\Check;
use SiteAudit\AuditResponse\AuditResponse;

class ModulePermissions extends Check {
  static public function getNamespace()
  {
    return 'variable/module_permissions';
  }

  public function check()
  {
    $context = $this->context;
    //If Module Permissions enabled check only admin has permission to administer list of modules
    if ($context->drush->moduleEnabled('module_permissions')) {
      $permissions = $context->drush->getRolesForPermission('administer module permissions');
      $roles = $context->drush->getAllRoles();

      foreach ($roles as $role) {
        if(in_array($role, $permissions)) {
          // For some reason when running against individual site it has an array key of 'role'
          // but when running against whole ACSF it has the key of 'label'
          if(isset($role['role'])) {
            $the_role = $role['role'];
          } else {
            $the_role = $role['label'];
          }
          if($the_role !== 'administrator') {
            return AuditResponse::AUDIT_FAILURE;
          }
        }
      }

      return AuditResponse::AUDIT_SUCCESS;
    } else {
      return AuditResponse::AUDIT_NA;
    }
  }
}
