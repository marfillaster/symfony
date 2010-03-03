<?php

namespace Symfony\Components\Validator\Specification;

class ConstraintSpecification
{
  private $name;
  private $options;
  private $groups = array('Symfony\Components\Validator\Groups\Base');

  public function __construct($name, $groups = null, array $options = array())
  {
    $this->name = $name;
    $this->options = $options;

    if (!is_null($groups))
    {
      $this->groups = (array)$groups;
    }
  }

  public function getName()
  {
    return $this->name;
  }

  public function getOption($name, $default = null)
  {
    return array_key_exists($name, $this->options) ? $this->options[$name] : $default;
  }

  public function getOptions()
  {
    return $this->options;
  }

  public function getGroups()
  {
    return $this->groups;
  }
}