<?php

namespace Symfony\Components\Validator\Mapping;

use Symfony\Components\Validator\ClassMetadataFactoryInterface;

class ClassMetadataFactory implements ClassMetadataFactoryInterface
{
  protected $loader;

  protected $loadedClasses = array();
  protected $loadedGroups = array();

  public function __construct(Loader\LoaderInterface $loader)
  {
    $this->loader = $loader;
  }

  public function getClassMetadata($class)
  {
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

  public function getGroupMetadata($interface)
  {
    if (!isset($this->loadedGroups[$interface]))
    {
      $metadata = new ClassMetadata($class);

      $this->loader->loadGroupMetadata($metadata);

      $this->loadedGroups[$interface] = $metadata;
    }

    return $this->loadedGroups[$interface];
  }
}