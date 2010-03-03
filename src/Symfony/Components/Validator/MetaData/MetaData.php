<?php

namespace Symfony\Components\Validator\MetaData;

use \ReflectionClass;
use Symfony\Components\Validator\MetaDataInterface;
use Symfony\Components\Validator\SpecificationInterface;

class MetaData implements MetaDataInterface
{
  protected $specification;
  protected $classMetaDatas = array();
  protected $groupMetaDatas = array();

  public function __construct(SpecificationInterface $specification)
  {
    $this->specification = $specification;
  }

  public function getClassMetaData($class)
  {
    if (!isset($this->classMetaDatas[$class]))
    {
      $this->getReflectionClassMetaData(new ReflectionClass($class));
    }

    return $this->classMetaDatas[$class];
  }

  public function getGroupMetaData($interface)
  {
    if (!isset($this->groupMetaDatas[$interface]))
    {
      $specification =  $this->specification->getGroupSpecification($interface);
      $this->groupMetaDatas[$interface] = new GroupMetaData($interface, $specification);
    }

    return $this->groupMetaDatas[$interface];
  }

  public function getGroupMetaDatas(array $interfaces)
  {
    $groups = array();

    foreach ($interfaces as $interface)
    {
      $groups[] = $this->getGroupMetaData($interface);
    }

    return $groups;
  }

  protected function getReflectionClassMetaData(ReflectionClass $class)
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