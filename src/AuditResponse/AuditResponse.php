<?php

namespace SiteAudit\AuditResponse;

use SiteAudit\Executor\DoesNotApplyException;
use Symfony\Component\Yaml\Parser;
use SiteAudit\Executor\ResultException;
use SiteAudit\Base\CheckInterface;

class AuditResponse {

  const AUDIT_NA = -1;
  const AUDIT_SUCCESS = 0;
  const AUDIT_WARNING = 1;
  const AUDIT_FAILURE = 2;
  const AUDIT_ERROR = 3;

  public $profile = [];

  protected $check;

  protected $status;

  protected $exception;

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
      $result = $callable($this->check);
      if (is_int($result)) {
        $this->setStatus($result);
      }
      else if (is_bool($result)) {
        switch ($result) {
          case TRUE:
            $this->setStatus(self::AUDIT_SUCCESS);
            break;

          case FALSE:
          default:
            $this->setStatus(self::AUDIT_FAILURE);
        }
      }
    }
    catch (DoesNotApplyException $e) {
      $this->setStatus(self::AUDIT_NA);
    }
    catch (ResultException $e) {
      $this->setStatus(self::AUDIT_ERROR);
      $this->exception = $e;
    }
    catch (\Exception $e) {
      $this->setStatus(self::AUDIT_ERROR);
      $this->exception = $e;
    }
  }

  /**
   * AuditResponse to string.
   */
  public function __toString() {
    try {
      switch ($this->status) {
        case self::AUDIT_SUCCESS :
          return '<info>' . $this->getMessage('success') . '</info>';
        case self::AUDIT_NA :
          return '<info>' . $this->getMessage('na') . '</info>';
        case self::AUDIT_WARNING :
          return '<comment>' . $this->getMessage('warning') . '</comment>';
        case self::AUDIT_FAILURE :
          return '<error>' . $this->getMessage('failure') . '</error>';
        case self::AUDIT_ERROR :
        default :
          return '<error>' . $this->getMessage('exception') . '</error>';
      }
    }
    catch (\Exception $e) {
      var_dump($e->getMessage());
    }
    return 'doh';
  }

  protected function getMessage($type = 'success') {
    if (!isset($this->profile['messages'][$type])) {
      throw new \Exception("Cannot format message. Unknown type $type.");
    }
    $message = $this->profile['messages'][$type];
    $tokens = $this->check->getTokens();
    if ($type == 'exception') {
      $tokens[':exception'] = $this->exception->getMessage();
    }
    return strtr($message, $tokens);
  }

  protected function setStatus($status) {
    $this->status = $status;
  }

  public function getStatus() {
    return $this->status;
  }

  public function getTitle() {
    return $this->profile['title'];
  }
}
