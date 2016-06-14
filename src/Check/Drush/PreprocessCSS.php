<?php

namespace SiteAudit\Check\Drush;

use SiteAudit\Check\Check;

class PreprocessCss extends Check {
  static public function getNamespace()
  {
    return 'variable/pagecache';
  }
  
  public function check()
  {
    $json = (int) $this->context->drush->getVariable('preprocess_css', 0);
    return (bool) $json;
  }
}
