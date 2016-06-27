<?php

namespace SiteAudit\Check\Drush;

use SiteAudit\Check\Check;

class ViewsSqlSignature extends Check {
  static public function getNamespace()
  {
    return 'views/views_sql_signature';
  }

  public function check()
  {
    if (!$this->context->drush->moduleEnabled('views')) {
      throw new \Exception("Views is not enabled on this site.");
    }
    $json = (int) $this->context->drush->getVariable('views_sql_signature', 0);
    return (bool) $json;
  }
}
