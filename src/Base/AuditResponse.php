<?php

namespace SiteAudit\Base;

use Symfony\Component\Console\Output\OutputInterface;

class AuditResponse {

  const AUDIT_SUCCESS = 0;
  const AUDIT_WARNING = 1;
  const AUDIT_FAILURE = 2;
  const AUDIT_ERROR = 3;

  protected $check;
  protected $description;
  protected $status;
  protected $message;
  protected $remediation;

  /**
   * AuditResponse to string.
   */
  public function __toString() {
    switch ($this->status) {
      case self::AUDIT_SUCCESS :
        return '<info>' . $this->message . '</info>';
      case self::AUDIT_WARNING :
        return '<comment>' . $this->message . '</comment>';
      default :
        return '<error>' . $this->message . '</error>';
    }
  }

  public function getStatus() {
    return $this->status;
  }

  public function hasPassed() {
    return $this->status === self::AUDIT_SUCCESS;
  }

  /**
   * @return mixed
   */
  public function getCheck() {
    return $this->check;
  }

  /**
   * @param mixed $check
   */
  public function setCheck($check) {
    $this->check = $check;
  }

  /**
   * @return mixed
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * @param mixed $description
   */
  public function setDescription($description) {
    $this->description = $description;
  }

  /**
   * @param mixed $remediation
   */
  public function setRemediation($remediation) {
    $this->remediation = $remediation;
  }

  /**
   * @param mixed $status
   */
  public function setSuccess($message) {
    $this->status = self::AUDIT_SUCCESS;
    $this->message = $message;
  }

  /**
   * @param mixed $status
   */
  public function setWarning($message) {
    $this->status = self::AUDIT_WARNING;
    $this->message = $message;
  }

  /**
   * @param mixed $status
   */
  public function setFailure($message) {
    $this->status = self::AUDIT_FAILURE;
    $this->message = $message;
  }

  /**
   * @param mixed $status
   */
  public function setError($message) {
    $this->status = self::AUDIT_ERROR;
    $this->message = $message;
  }

}
