<?php

namespace Symfony\Components\Validator\MetaData;

use \ReflectionClass;
use Symfony\Components\Validator\SpecificationInterface;
use Symfony\Components\Validator\Exception\ValidatorException;

class ConstraintFinder
{
  protected $metaData;
  protected $groups = array('Symfony\Components\Validator\Groups\Base');

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
        foreach ($this->groups as $searchedGroup)
        {
          $searchedGroup = new ReflectionClass($searchedGroup);

          foreach ($constraint->getGroups() as $constraintGroup)
          {
            if ($searchedGroup->implementsInterface($constraintGroup))
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