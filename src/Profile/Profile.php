<?php

namespace SiteAudit\Profile;

use Symfony\Component\ClassLoader\ClassMapGenerator;
use Symfony\Component\Yaml\Yaml;
use SiteAudit\Check\Registry;

class Profile
{
  const DIRECTORY = 'profiles';

  protected $title;
  protected $machine_name;
  protected $checks = array();

  /**
   * Set the Profile title
   */
  public function setTitle($title)
  {
    $this->title = $title;
    return $this;
  }

  /**
   * Get the Profile title
   */
  public function getTitle()
  {
    return $this->title;
  }

  /**
   * Set the Profile machine name
   */
  public function setMachineName($machine_name)
  {
    $this->machine_name = $machine_name;
    return $this;
  }

  /**
   * Get the Profile machine name
   */
  public function getMachineName()
  {
    return $this->machine_name;
  }

  /**
   * Add a check to the profile.
   */
  public function addCheck($check, $options = array())
  {
    $registry = Registry::load();
    $reverse_lookup = array_flip($registry);

    // Look for the check in the register as the class name.
    if (isset($registry[$check])) {
      $this->checks[$check] = $options;
    }
    // Look for the check in the register as the namespace.
    elseif (isset($reverse_lookup[$check])) {
      $class = $reverse_lookup[$check];
      $this->checks[$class] = $options;
    }
    else {
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Get the Profile machine name
   */
  public function getChecks()
  {
    return $this->checks;
  }

  public function save()
  {
    $data['metadata']['title'] = $this->getTitle();
    $data['metadata']['machine_name'] = $this->getMachineName();
    $data['checks'] = $this->checks;

    $yaml = Yaml::dump($data);

    return file_put_contents($this->getFilepath(), $yaml);
  }

  public function load($machine_name)
  {
    if (!file_exists($this->getFilepath($machine_name))) {
      return FALSE;
    }
    $yaml = file_get_contents($this->getFilepath($machine_name));
    $data = Yaml::parse($yaml);
    $this->setTitle($data['metadata']['title'])
         ->setMachineName($data['metadata']['machine_name']);
    $this->checks = $data['checks'];
  }

  public function getFilepath($machine_name = NULL)
  {
    if (empty($machine_name)) {
      $machine_name = $this->getMachineName();
    }
    $filename = $machine_name . '.yml';
    return self::DIRECTORY . '/' . $filename;
  }

}

 ?>
