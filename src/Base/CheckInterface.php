<?php

namespace SiteAudit\Base;

use Symfony\Component\Console\Input\InputInterface;
use SiteAudit\Base\Context;

Interface CheckInterface {

  public function __construct(Context $context, Array $options);

  public function check();
}
