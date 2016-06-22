<?php

namespace SiteAudit\Check\D7;

use SiteAudit\Check\Check;

class BlacklistPermissions extends Check {
  static public function getNamespace()
  {
    return 'system/blacklist_permissions';
  }

  public function check() {
    global $argv;
    $perms = $this->getOption('permissions');
    if (empty($perms)) {
      return TRUE;
    }

    $where = [];
    foreach ($perms as $perm) {
      $where[] = 'rp.permission = \'' . $perm . '\'';
    }

    // We don't care about the 'administrator' role having access.
    $admin_role = $this->context->drush->getVariable('user_admin_role', 0);

    try {
      $output = $this->context->drush->sqlQuery('SELECT r.rid, r.name, rp.permission FROM role r INNER JOIN role_permission rp ON rp.rid = r.rid WHERE r.rid != ' . $admin_role . ' AND (' . implode(' OR ', $where) . ');');
      $output = array_filter($output);
    }
    catch (\Exception $e) {
      return FALSE;
    }
    if (empty($output)) {
      return TRUE;
    }

    $black_roles = [];
    foreach ($output as $line) {
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
