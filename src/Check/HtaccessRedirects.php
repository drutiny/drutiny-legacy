<?php

namespace SiteAudit\Check;

class HtaccessRedirects extends Check {
  protected function getNamespace()
  {
    return 'system/htaccess_redirects';
  }
  public function check()
  {
    $context = $this->context;
    $patterns = array(
      'RedirectPermanent',
      'Redirect(Match)?.*?(301|permanent) *$',
      'RewriteRule.*\[.*R=(301|permanent).*\] *$'
    );
    $regex = '^ *(' . implode('|', $patterns) . ')';
    $command = "grep -Ei '${regex}' {$context->config['root']}/.htaccess | wc -l";
    $output = (int) (string) $context->remoteExecutor->execute($command);

    $this->setToken('output', $output);

    return $output < $this->getOption('max_redirects', 10);
  }
}
