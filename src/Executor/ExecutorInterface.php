<?php

namespace SiteAudit\Executor;

use Symfony\Component\Console\Output\OutputInterface;

Interface ExecutorInterface {
  public function __construct(OutputInterface $output);
  public function execute($command);
}
