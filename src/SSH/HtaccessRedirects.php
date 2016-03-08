<?php

namespace SiteAudit\SSH;

use SiteAudit\Base\AuditResponse;

class HtaccessRedirects extends SSHCheck {
  public function check() {
    $patterns = array(
      'RedirectPermanent',
      'Redirect(Match)?.*?(301|permanent) *$',
      'RewriteRule.*\[.*R=(301|permanent).*\] *$'
    );
    $regex = '^ *(' . implode('|', $patterns) . ')';
    $command = "grep -Ei '${regex}' {$this->root}/.htaccess | wc -l";
    $output = (int) $this->executeSSHCommand($command);

    $max_redirects = 10;
    if (isset($this->options['max_redirects'])) {
      $max_redirects = (int) $this->options['max_redirects'];
    }

    $response = new AuditResponse();
    $response->setDescription('When there are a large number of redirects in the .htaccess file they are all required to be loaded at run time during every request as Apache needs to analyze the contents so that it can make appropriate decisions about how to process the application and incoming requests. Redirect rules should be refactored to take advantage of regular expressions if possible. Otherwise the redirect module should be added to the site and all of the redirects in the .htaccess file should be moved into the Drupal site. Although these redirects will then require a Drupal bootstrap in order to fulfill the request, Varnish will be able to cache the redirect once it has been made once as long as there is a maximum age set on the site.');
    $response->setRemediation("Reduce the number of redirects in the .htaccess file");
    if ($output < $max_redirects) {
      $response->setSuccess(".htaccess redirects less than ${max_redirects} redirects, actual value ${output} redirect(s)");
    }
    else {
      $response->setFailure(".htaccess redirects less than ${max_redirects} redirects, actual value ${output} redirect(s)");
    }

    $this->output->writeln((string) $response);
  }
}
