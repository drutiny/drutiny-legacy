<?php

namespace SiteAudit\Executor;

class Result {
  protected $command;
  protected $output;
  protected $return_val;

  public function __construct($command) {
    $output = [];
    $return_val = 0;
    // Supress StdErr output.
    $command .= ' 2> /dev/null';
    exec($command, $output, $return_val);

    // Datetime weirdness. Apparently this is caused by theming issues on the
    // remote theme. Why it is being called when executed via CLI is another
    // story.
    if (isset($output[0])) {
      if (strpos($output[0], 'date_timezone_set() expects parameter') === 0) {
        array_shift($output);
      }
      if (strpos($output[0], 'date_format() expects parameter') === 0) {
        array_shift($output);
      }
    }

    $this->command = $command;
    $this->output = $output;
    $this->return_val = $return_val;

    if (!$this->isSuccessful()) {
      throw new ResultException("Command failed: $command", $this);
    }
  }

  public function __toString() {
    return trim(implode(PHP_EOL, array_filter($this->output)));
  }

  public function isSuccessful() {
    return $this->return_val === 0;
  }

  public function getOutput() {
    return $this->output;
  }

  public function getText() {
    return (string) $this;
  }

  public function parseJson($assoc = FALSE) {
    return json_decode((string)$this, $assoc);
  }

}
