<?php

namespace SiteAudit\AuditResponse;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Parser;
use SiteAudit\Executor\ResultException;
use SiteAudit\Base\CheckInterface;

class AuditResponse {

  const AUDIT_SUCCESS = 0;
  const AUDIT_WARNING = 1;
  const AUDIT_FAILURE = 2;
  const AUDIT_ERROR = 3;

  protected $profile = [];

  protected $check;

  protected $status;

  public function __construct($namespace, CheckInterface $check) {
    $filename = dirname(__FILE__) . '/' . $namespace . '.yml';

    if (!file_exists($filename)) {
      throw new \Exception("No namespace found for $namespace");
    }

    $parser = new Parser();
    $this->profile = $parser->parse(file_get_contents($filename));

    $this->check = $check;

  }

  public function test(callable $callable) {
    try {
      switch ($callable($this->check)) {
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
        return '<info>' . $this->getMessage('success') . '</info>';
      case self::AUDIT_FAILURE :
        return '<comment>' . $this->getMessage('failure') . '</comment>';
      case self::AUDIT_ERROR :
      default :
        return '<error>' . $this->getMessage('exception') . '</error>';
    }
  }

  protected function getMessage($type = 'success') {
    if (!isset($this->profile['messages'][$type])) {
      throw new \Exception("Cannot format message. Unkonwn type $type.");
    }
    $message = $this->profile['messages'][$type];
    return strtr($message, $this->check->getTokens());
  }

  protected function setStatus($status) {
    $this->status = $status;
  }
}
