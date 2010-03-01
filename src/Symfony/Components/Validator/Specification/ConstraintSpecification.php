<?php

namespace Symfony\Components\Validator\Specification;

class ConstraintSpecification
{
  protected $name;

  protected $options;

  protected $groups = array('default');

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

  public function getOptions()
  {
    return $this->options;
  }

  public function getGroups()
  {
    return $this->groups;
  }
}