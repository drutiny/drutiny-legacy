<?php

namespace SiteAudit\Executor;

class ResultException extends \Exception {
  protected $result;

  public function __construct($message, Result $result) {
    $this->result = $result;
  }
}
