<?php

namespace Symfony\Components\Validator\Specification\Builder;

use Symfony\Components\Validator\Specification\GroupSpecification;

class GroupSpecificationBuilder
{
  protected $interface;

  protected $groupSequence = array();

  public function __construct($interface)
  {
    $this->interface = $interface;
  }

  public function setGroupSequence(array $groupSequence)
  {
    $this->groupSequence = $groupSequence;

    return $this;
  }

  public function getGroupSpecification()
  {
    return new GroupSpecification(
      $this->interface,
      $this->groupSequence
    );
  }
}