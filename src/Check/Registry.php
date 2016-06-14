<?php

namespace SiteAudit\Check;

use Symfony\Component\ClassLoader\ClassMapGenerator;

class Registry
{
  static protected $registry = array();

  /**
   * Retrieve a list of Check classes.
   */
  static public function load() {
    if (empty(self::$registry)) {
      $checkDir = realpath(__DIR__ . '/../Check');
      $map = ClassMapGenerator::createMap($checkDir);

      foreach ($map as $class => $filepath) {
        $reflect = new \ReflectionClass($class);
        if ($reflect->isAbstract()) {
          continue;
        }
        if ($reflect->isSubClassOf('SiteAudit\Check\Check')) {
          self::$registry[$class] = $class::getNamespace();
        }
      }
    }
    return self::$registry;
  }
}

 ?>
