<?php

namespace SiteAudit\Check\D7;

use SiteAudit\Check\Check;

class BlacklistPermissions extends Check {
  static public function getNamespace()
  {
    return 'system/blacklist_permissions';
  }

  public function check() {
    $perms = $this->getOption('permissions');
    if (empty($perms)) {
      return TRUE;
    }

    $where = [];
    foreach ($perms as $perm) {
      $where[] = "(rp.permission LIKE '$perm')";
    }
    $sql = "SELECT r.rid, r.name, rp.permission FROM role r INNER JOIN role_permission rp ON rp.rid = r.rid WHERE " . implode(' OR ', $where);

    try {
      $result = $this->context->drush->sqlq($sql);
    }
    catch (\Exception $e) {
      return FALSE;
    }
    if (empty($result->getOutput())) {
      return TRUE;
    }

    $roles = [];
    $black_roles = [];
    foreach ($result->getOutput() as $line) {
      list($rid, $role, $permission) = explode("\t", $line);
      $black_roles[$role][] = $permission;
    }

    foreach ($black_roles as $role => &$perms) {
      $perms = $role . ': ' . implode(', ', $perms);
    }

    $this->setToken('roles', implode("\n", $black_roles));

    return FALSE;
  }
}
