<?php

namespace SiteAudit\AuditResponse;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Parser;
use SiteAudit\Executor\ResultException;

class AuditResponse {

  const AUDIT_SUCCESS = 0;
  const AUDIT_WARNING = 1;
  const AUDIT_FAILURE = 2;
  const AUDIT_ERROR = 3;

  protected $profile = [];

  protected $status;

  public function __construct($namespace) {
    $filename = dirname(__FILE__) . '/' . $namespace . '.yml';

    if (!file_exists($filename)) {
      throw new \Exception("No namespace found for $namespace");
    }

    $parser = new Parser();
    $this->profile = $parser->parse(file_get_contents($filename));

  }

  public function test(callable $callable) {
    try {
      switch ($callable()) {
        case TRUE:
          $this->setStatus(self::AUDIT_SUCCESS);
          break;

        case FALSE:
        default:
          $this->setStatus(self::AUDIT_FAILURE);
      }
    }
    catch (ResultException $e) {
      $this->setStatus(self::AUDIT_ERROR);
    }
  }

  /**
   * AuditResponse to string.
   */
  public function __toString() {
    switch ($this->status) {
      case self::AUDIT_SUCCESS :
        return '<info>' . $this->profile['messages']['success'] . '</info>';
      case self::AUDIT_FAILURE :
        return '<comment>' . $this->profile['messages']['failure'] . '</comment>';
      case self::AUDIT_ERROR :
      default :
        return '<error>' . $this->profile['messages']['exception'] . '</error>';
    }
  }

  protected function setStatus($status) {
    $this->status = $status;
  }
}
