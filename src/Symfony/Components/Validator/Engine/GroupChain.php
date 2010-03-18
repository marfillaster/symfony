<?php

namespace Symfony\Components\Validator\Engine;

use Symfony\Components\Validator\Mapping\ClassMetadata;

class GroupChain
{
  protected $groups = array();
  protected $groupSequences = array();

  public function addGroup(ClassMetadata $group)
  {
    $this->groups[$group->getName()] = $group;
  }

  public function addGroupSequence(array $groups)
  {
    if (count($groups) == 0)
    {
      throw new \InvalidArgumentException('A group sequence must contain at least one group');
    }

    foreach ($groups as $group)
    {
      if (!$group instanceof ClassMetadata)
      {
        throw new \InvalidArgumentException('Groups must be instance of ClassMetadata');
      }
    }

    if (!in_array($groups, $this->groupSequences, true))
    {
      $this->groupSequences[] = $groups;
    }
  }

  public function getGroups()
  {
    return $this->groups;
  }

  public function getGroupSequences()
  {
    return $this->groupSequences;
  }
}