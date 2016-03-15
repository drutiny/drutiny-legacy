<?php

namespace SiteAudit\Base;

use SiteAudit\Base\Context;
use SiteAudit\Base\CheckInterface;

abstract class Check implements CheckInterface {

  protected $context;
  private $options;

  public function __construct(Context $context, Array $options) {
    $this->context = $context;
    $this->options = $options;
  }

  protected function getOption($name, $default = NULL) {
    return empty($this->options[$name]) ? $default : $this->options[$name];
  }

  protected function setToken($name, $value) {
    $this->options[$name] = $value;
    return $this;
  }

  public function getTokens() {
    $tokens = [];
    foreach ($this->options as $key => $value) {
      $tokens[':' . $key] = $value;
    }
    return $tokens;
  }

  abstract public function check();
}
