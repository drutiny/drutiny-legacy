<?php

namespace SiteAudit\Check\Drush;

use SiteAudit\Check\Check;

class PreprocessCss extends Check {
  protected function getNamespace()
  {
    return 'variable/pagecache';
  }
  public function check()
  {
    $context = $this->context;
    $json = (int) $context->drush->getVariable('preprocess_css', 0);
    return (bool) $json;
  }
}
