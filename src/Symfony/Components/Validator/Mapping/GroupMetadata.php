<?php

namespace Symfony\Components\Validator\Mapping;

use Symfony\Components\Validator\Specification\GroupSpecification;

// IMMUTABLE object
class GroupMetadata
{
  private $interfaceName;
  private $groupSequence;
  private $refClass;

  public function __construct($interfaceName, array $groupSequence = array())
  {
    $this->interfaceName = $interfaceName;
    $this->groupSequence = $groupSequence;
    $this->refClass = new ReflectionClass($interfaceName);

    // TODO: circle detection
  }

  public function __toString()
  {
    return $this->getInterfaceName();
  }

  public function getInterfaceName()
  {
    return $this->interfaceName;
  }

  public function isGroupSequence()
  {
    return count($this->groupSequence) > 0;
  }

  public function getGroupSequence()
  {
    return $this->groupSequence;
  }

//  public function isInstanceOf($group)
//  {
//    if ($group instanceof GroupMetadata)
//    {
//      $group = $group->getInterfaceName();
//    }
//
//    return $this->refClass->implementsInterface($group);
//  }
}