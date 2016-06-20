<?php

namespace SiteAudit\Check\Drush;

use SiteAudit\Check\Check;

class UserRegister extends Check {
  static public function getNamespace()
  {
    return 'variable/user_register';
  }

  public function check()
  {
    // @see USER_REGISTER_VISITORS_ADMINISTRATIVE_APPROVAL.
    $user_register = (int) $this->context->drush->getVariable('user_register', 2);
    return $user_register === 0;
  }
}
