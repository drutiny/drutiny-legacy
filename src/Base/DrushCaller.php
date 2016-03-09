<?php

namespace SiteAudit\Base;

use SiteAudit\Executor\ExecutorInterface;

class DrushCaller {
  protected $executor;

  protected $alias;

  public function __construct(ExecutorInterface $executor) {
    $this->executor = $executor;
  }

  public function setAlias($alias) {
    $this->alias = $alias;
    return $this;
  }

  public function __call($method, $args) {
    // Convert method from camelCase to Drush hyphen based method naming.
    // E.g. PmInfo will become pm-info.
    preg_match_all('/((?:^|[A-Z])[a-z]+)/',$method,$matches);
    $method = implode('-', array_map('strtolower', $matches[0]));

    $command = ['drush'];
    if (!empty($this->alias)) {
      $command[] = '@' . $this->alias;
    }

    $command[] = $method;
    $command = array_merge($command, $args);
    return $this->executor->execute(implode(' ', $command));
  }

  /**
   * Try to find out if a module is enabled or not.
   *
   * @param $name
   * @return bool
   *   TRUE if the module is enabled, FALSE otherwise (including if the module
   *   was not found).
   */
  public function getModuleStatus($name) {
    try {
      $response = $this->pmInfo($name, '--format=json')->parseJson();
      $disabled = ($response->{$name}->status === "not installed");
      return !$disabled;
    }
    // The response from Drush can be "No matching variable found.", even with
    // JSON being requested, which is weird.
    catch (\Exception $e) {
      return FALSE;
    }
  }
}
