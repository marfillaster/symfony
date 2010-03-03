<?php

namespace Symfony\Components\Validator\MetaData;

use \ReflectionClass;
use Symfony\Components\Validator\Specification\GroupSpecification;

// IMMUTABLE object
class GroupMetaData
{
  private $interfaceName;
  private $groupSequence;

  public function __construct($interfaceName, GroupSpecification $specification = null)
  {
    $this->interfaceName = $interfaceName;

    if (!is_null($specification))
    {
      $this->groupSequence = $specification->getGroupSequence();
    }
    else
    {
      $this->groupSequence = array($interfaceName);
    }
  }

  public function getInterfaceName()
  {
    return $this->interfaceName;
  }

  public function getGroupSequence()
  {
    return $this->groupSequence;
  }

  public function isInstanceOf($interfaceName)
  {
    return is_subclass_of($this->interfaceName, $interfaceName);
  }
}