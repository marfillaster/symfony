<?php

namespace Symfony\Components\Validator\MetaData;

use \ReflectionClass;
use Symfony\Components\Validator\SpecificationInterface;
use Symfony\Components\Validator\MetaData\GroupMetaData;
use Symfony\Components\Validator\Exception\ValidatorException;

class ConstraintFinder
{
  protected $metaData;
  protected $groups;

  public function __construct(ElementMetaData $metaData)
  {
    $this->metaData = $metaData;
    $this->groups = array(new GroupMetaData('Symfony\Components\Validator\Groups\Base'));
  }

  public function inGroups($groups)
  {
    $this->groups = is_array($groups) ? $groups : array($groups);

    return $this;
  }

  public function getConstraints()
  {
    $constraints = array();

    if (!is_null($this->metaData))
    {
      foreach ($this->metaData->getConstraints() as $constraint)
      {
        foreach ($this->groups as $searchedGroup)
        {
          foreach ($constraint->getGroups() as $constraintGroup)
          {
            if ($searchedGroup->isInstanceOf($constraintGroup))
            {
              $constraints[$constraint->getName()] = $constraint;
              break 2;
            }
          }
        }
      }
    }

    return $constraints;
  }
}