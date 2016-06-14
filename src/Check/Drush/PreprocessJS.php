<?php

namespace SiteAudit\Check\Drush;

use SiteAudit\Check\Check;

class PreprocessJS extends Check {
  static public function getNamespace()
  {
    return 'variable/preprocess_js';
  }

  public function check()
  {
    $json = (int) $this->context->drush->getVariable('preprocess_js', 0);
    return (bool) $json;
  }
}
