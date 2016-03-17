<?php

namespace SiteAudit\SSH;

use SiteAudit\AuditResponse\AuditResponse;
use SiteAudit\Base\Check;

class HtaccessRedirects extends Check {
  public function check() {
    $response = new AuditResponse('system/htaccess_redirects', $this);
    $context = $this->context;

    $response->test(function ($check) {
      $context = $check->context;
      $patterns = array(
        'RedirectPermanent',
        'Redirect(Match)?.*?(301|permanent) *$',
        'RewriteRule.*\[.*R=(301|permanent).*\] *$'
      );
      $regex = '^ *(' . implode('|', $patterns) . ')';
      $command = "grep -Ei '${regex}' {$context->config['root']}/.htaccess | wc -l";
      $output = (int) (string) $context->remoteExecutor->execute($command);

      $check->setToken('output', $output);

      return $output < $check->getOption('max_redirects', 10);
    });

    return $response;
  }
}
