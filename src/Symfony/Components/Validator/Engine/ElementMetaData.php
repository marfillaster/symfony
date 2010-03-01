<?php

namespace Symfony\Components\Validator\Engine;

use Symfony\Components\Validator\Specification\ElementSpecification;

// IMMUTABLE
class ElementMetaData
{
  private $className;
  private $constraints = array();

  public function __construct($className, ElementSpecification $specification = null, ElementMetaData $parent = null)
  {
    $this->className = $className;

    if (!is_null($specification))
    {
      $this->constraints = $specification->getConstraints();
    }

    if (!is_null($parent))
    {
      $this->mergeConstraints($parent);
    }
  }

  public function getClassName()
  {
    return $this->className;
  }

  public function getConstraints()
  {
    return $this->constraints;
  }

  public function hasConstraints()
  {
    return count($this->constraints) > 0;
  }

  public function findConstraints()
  {
    return new ConstraintFinder($this);
  }

  protected function mergeConstraints(ElementMetaData $metaData)
  {
    foreach ($metaData->constraints as $name => $constraint)
    {
      if (!isset($this->constraints[$name]))
      {
        $this->constraints[$name] = $constraint;
      }
    }
  }
}