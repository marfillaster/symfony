<?php

namespace Symfony\Components\Validator\Engine;

use Symfony\Components\Validator\SpecificationInterface;
use Symfony\Components\Validator\Exception\ValidatorException;

class ConstraintFinder
{
  protected $metaData;
  protected $groups = array('default');

  public function __construct(ElementMetaData $metaData)
  {
    $this->metaData = $metaData;
  }

  public function inGroups($groups)
  {
    $this->groups = (array)$groups;

    return $this;
  }

  public function getConstraints()
  {
    $constraints = array();

    if (!is_null($this->metaData))
    {
      foreach ($this->metaData->getConstraints() as $constraint)
      {
        // more efficient than array_intersect()
        foreach ($constraint->getGroups() as $constraintGroup)
        {
          foreach ($this->groups as $searchedGroup)
          {
            if ($searchedGroup == $constraintGroup)
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