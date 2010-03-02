<?php

namespace Symfony\Components\Validator\Engine;

use Symfony\Components\Validator\SpecificationInterface;

class ClassMetaDataCache
{
  protected $specification;
  protected $classMetaDatas = array();

  public function __construct(SpecificationInterface $specification)
  {
    $this->specification = $specification;
  }

  public function getClassMetaData($className)
  {
    if (!isset($this->classMetaDatas[$className]))
    {
      $this->getReflectionClassMetaData(new \ReflectionClass($className));
    }

    return $this->classMetaDatas[$className];
  }

  protected function getReflectionClassMetaData(\ReflectionClass $class)
  {
    $className = $class->getName();

    if (!isset($this->classMetaDatas[$className]))
    {
      $specification = $this->specification->getClassSpecification($className);

      if ($parentClass = $class->getParentClass())
      {
        $parent = $this->getReflectionClassMetaData($parentClass);
      }
      else
      {
        $parent = null;
      }

      $interfaces = array();
      foreach ($class->getInterfaces() as $interface)
      {
        $interfaces[] = $this->getReflectionClassMetaData($interface);
      }

      $this->classMetaDatas[$className] = new ClassMetaData($className, $specification, $parent, $interfaces);
    }

    return $this->classMetaDatas[$className];
  }
}