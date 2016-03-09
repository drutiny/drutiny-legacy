<?php

namespace SiteAudit\Base;

use SiteAudit\Base\Context;
use SiteAudit\Base\CheckInterface;

abstract class Check implements CheckInterface {

  protected $context;
  protected $options;

  public function __construct(Context $context, Array $options) {
    $this->context = $context;
    $this->options = $options;
  }

  protected function getOption($name, $default = NULL) {
    return empty($this->options[$name]) ? $default : $this->options[$name];
  }

  abstract public function check();
}
