<?php

namespace Symfony\Components\Validator\Engine;

class GroupSequence implements \IteratorAggregate
{
  protected $groups = array();

  public function __construct(array $groups)
  {
    $this->append($groups);
  }

  protected function append(array $groups)
  {
    foreach ($groups as $group)
    {
      foreach ($this->groups as $seqGroup)
      {
        if ($seqGroup->isInstanceOf($group))
        {
          continue 2;
        }
      }

      if ($group->isGroupSequence())
      {
        $this->append($group->getGroupSequence());
      }
      else
      {
        $this->groups[] = $group;
      }
    }
  }

  public function getIterator()
  {
    return new \ArrayIterator($this->groups);
  }
}