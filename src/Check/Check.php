<?php

namespace SiteAudit\Check;

use SiteAudit\Context;
use SiteAudit\AuditResponse\AuditResponse;
use SiteAudit\Executor\DoesNotApplyException;
use SiteAudit\Executor\ResultException;

abstract class Check {

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

  /**
   * Run a sandboxed check.
   *
   * @return int AuditResponse::AUDIT_SUCCESS or similar.
   */
  abstract protected function check();

  /**
   * The namespace AuditReponse should use to discover a .yml file for check.
   */
  static public function getNamespace()
  {
    return 'not/available';
  }

  /**
   * Execute the check in a sandbox.
   *
   * @return AuditResponse the outcome of the check.
   */
  public function execute()
  {
    $response = new AuditResponse($this::getNamespace(), $this);

    try {
      $result = $this->check();

      // All constants are integers, check for them first.
      if (is_int($result)) {
        $response->setStatus($result);
      }
      // Booleans can also be used.
      else {
        switch ($result) {
          case TRUE:
            $response->setStatus(AuditResponse::AUDIT_SUCCESS);
            break;

          case FALSE:
            $response->setStatus(AuditResponse::AUDIT_FAILURE);
            break;
        }
      }

    }
    catch (DoesNotApplyException $e) {
      $response->setStatus(AuditResponse::AUDIT_NA);
    }
    catch (ResultException $e) {
      $response->setStatus(AuditResponse::AUDIT_ERROR);
      $response->exception = $e;
    }
    catch (\Exception $e) {
      $response->setStatus(AuditResponse::AUDIT_ERROR);
      $response->exception = $e;
    }
    return $response;
  }
}
