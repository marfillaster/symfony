<?php

namespace Symfony\Components\Validator\Specification;

class GroupSpecification
{
  private $className;
  private $interfaceName;
  private $groupSequence;

  public function __construct($interfaceName, array $groupSequence = null)
  {
    $this->interfaceName = $interfaceName;
    $this->groupSequence = is_null($groupSequence) ? array() : $groupSequence;
  }

  public function getInterfaceName()
  {
    return $this->interfaceName;
  }

  public function getGroupSequence()
  {
    return $this->groupSequence;
  }
}