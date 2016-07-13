<?php

namespace SiteAudit\Check\Drush;

use SiteAudit\Check\Check;
use SiteAudit\Annotation\CheckInfo;

/**
 * @CheckInfo(
 *  title = "Anonymous sessions",
 *  description = "If you are generating sessions for anonymous users, you are causing a major performance impact to your site. Having anonymous sessions will break traditional page caching in Varnish and CDNs.",
 *  remediation = "Find out what modules are causing the sessions, and look to remove them.",
 *  success = "There are no anonymous sessions.",
 *  failure = "There are <code>:number_of_anon_sessions</code> anonymous sessions.",
 *  exception = "Could not determine the amount of anonymous sessions.",
 * )
 */
class SessionsAnon extends Check {
  public function check() {

    $output = $this->context->drush->sqlQuery("SELECT COUNT(*) FROM sessions WHERE uid = 0;");
    if (empty($output)) {
      $number_of_anon_sessions = 0;
    }
    else if (count($output) == 1) {
      $number_of_anon_sessions = (int) $output[0];
    }
    else {
      $number_of_anon_sessions = (int) $output[1];
    }
    $this->setToken('number_of_anon_sessions', $number_of_anon_sessions);

    return $number_of_anon_sessions === 0;
  }
}
