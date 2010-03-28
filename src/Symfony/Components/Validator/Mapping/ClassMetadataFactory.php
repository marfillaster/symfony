<?php

namespace Symfony\Components\Validator\Mapping;

use Symfony\Components\Validator\ClassMetadataFactoryInterface;

class ClassMetadataFactory implements ClassMetadataFactoryInterface
{
  protected $loader;

  protected $loadedClasses = array();

  public function __construct(Loader\LoaderInterface $loader)
  {
    $this->loader = $loader;
  }

  public function getClassMetadata($class)
  {
    $class = ltrim($class, '\\');

    if (!isset($this->loadedClasses[$class]))
    {
      $metadata = new ClassMetadata($class);

      // Include constraints from the parent class
      if ($parent = $metadata->getReflectionClass()->getParentClass())
      {
        $this->mergeConstraints($this->getClassMetadata($parent->getName()), $metadata);
      }

      // Include constraints from all implemented interfaces
      foreach ($metadata->getReflectionClass()->getInterfaces() as $interface)
      {
        $this->mergeConstraints($this->getClassMetadata($interface->getName()), $metadata);
      }

      $this->loader->loadClassMetadata($metadata);

      $this->loadedClasses[$class] = $metadata;
    }

    return $this->loadedClasses[$class];
  }

  public function getClassMetadatas(array $classes)
  {
    foreach ($classes as $key => $class)
    {
      $classes[$key] = $this->getClassMetadata($class);
    }

    return $classes;
  }

  protected function mergeConstraints(ClassMetadata $from, ClassMetadata $to)
  {
    foreach ($from->getConstraints() as $constraint)
    {
      $to->addConstraint($constraint);
    }

    foreach ($from->getConstrainedProperties() as $property)
    {
      $propMetadata = $from->getPropertyMetadata($property);

      foreach ($propMetadata->getConstraints() as $constraint)
      {
        $to->addPropertyConstraint($property, $constraint);
      }
    }
  }
}