<?php

namespace SiteAudit\Base;

use SiteAudit\Executor\ExecutorInterface;

class DrushCaller {
  protected $executor;

  protected $alias;
  protected $args = [];

  public function __construct(ExecutorInterface $executor) {
    $this->executor = $executor;
  }

  public function setAlias($alias) {
    $this->alias = $alias;
    return $this;
  }

  public function setArgument($arg) {
    $this->args[] = $arg;
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

    foreach ($this->args as $arg) {
      $command[] = $arg;
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
  public function moduleEnabled($name) {
    try {
      $response = $this->pmInfo($name, '--format=json')->parseJson();

      // Drush can return with non-JSON responses even though you specify you
      // want JSON.
      // e.g. "acsf_openid was not found."
      if (is_null($response)) {
        return FALSE;
      }

      $status = $response->{$name}->status;
      $disabled = ($status === "not installed" || $status === 'disabled');
      return !$disabled;
    }
    catch (\Exception $e) {
      return FALSE;
    }
  }

  /**
   * Try to return a variable value.
   *
   * @param $name
   *   The name of the variable (exact).
   * @param int $default
   *   The value to return if the variable is not set.
   * @return mixed
   */
  public function getVariable($name, $default = 0) {
    try {
      $result = $this->variableGet($name, '--exact --format=json')->parseJson(TRUE);
      if (isset($result[$name])) {
        return $result[$name];
      }
      return $default;
    }
    // The response from Drush can be "No matching variable found.", even with
    // JSON being requested, which is weird.
    catch (\Exception $e) {
      return $default;
    }
  }
}
