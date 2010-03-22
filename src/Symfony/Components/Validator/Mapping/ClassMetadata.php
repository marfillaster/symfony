<?php

namespace Symfony\Components\Validator\Mapping;

use Symfony\Components\Validator\Constraints\Constraint;

class ClassMetadata extends ElementMetadata
{
  const DEFAULT_GROUP = 'Symfony\Components\Validator\Groups\Base';

  protected $name;
  protected $properties = array();
  protected $groupSequence = array();
  protected $groupAliases = array();
  protected $reflClass;
  protected $reflProperties;

  public function __construct($name, array $knownGroups = array())
  {
    $this->name = $name;
    $this->reflClass = new \ReflectionClass($name);

    foreach ($knownGroups as $group)
    {
      $className = substr($group, strrpos($group, '\\'));
      $this->groupAliases[$className] = $group;
    }
  }

  public function getName()
  {
    return $this->name;
  }

  public function getReflectionClass()
  {
    return $this->reflClass;
  }

  protected function getReflectionProperty($property)
  {
    if (!isset($this->reflProperties[$property]))
    {
      $reflProp = $this->reflClass->getProperty($property);
      $reflProp->setAccessible(true);

      $this->reflProperties[$property] = $reflProp;
    }

    return $this->reflProperties[$property];
  }

  public function addConstraint(Constraint $constraint)
  {
    // TODO: testen
    $constraint->groups = $this->resolveGroupNames((array)$constraint->groups);

    $this->addImplicitGroupNames($constraint);

    parent::addConstraint($constraint);
  }

  public function addPropertyConstraint($name, Constraint $constraint)
  {
    if (!isset($this->properties[$name]))
    {
      $this->properties[$name] = new PropertyMetadata($name);
    }

    // TODO: testen
    $constraint->groups = $this->resolveGroupNames((array)$constraint->groups);

    $this->addImplicitGroupNames($constraint);

    $this->properties[$name]->addConstraint($constraint);
  }

  protected function addImplicitGroupNames(Constraint $constraint)
  {
    if (in_array(self::DEFAULT_GROUP, $constraint->groups) && !in_array($this->name, $constraint->groups))
    {
      $constraint->groups[] = $this->name;
    }
  }

  public function getPropertyMetadata($name)
  {
    // TODO error treatment

    return $this->properties[$name];
  }

  public function getConstrainedProperties()
  {
    return array_keys($this->properties);
  }

  public function getPropertyValue($object, $property)
  {
    $getter = 'get'.ucfirst($property);
    $isser = 'is'.ucfirst($property);

    if (property_exists($object, $property))
    {
      $value = $this->getReflectionProperty($property)->getValue($object);
    }
    else if (method_exists($object, $getter))
    {
      $value = $object->$getter();
    }
    else
    {
      throw new ValidatorException(sprintf('Neither property "%s" nor method "%s" is readable in class "%s"', $property, $getter, get_class($object)));
    }

    return $value;
  }

  // TODO: testen
  public function resolveGroupName($group)
  {
    if ($group == $this->reflClass->getShortName())
    {
      return $this->name;
    }
    else if ($group == 'Base')
    {
      return self::DEFAULT_GROUP;
    }
    else if (isset($this->groupAliases[$group]))
    {
      return $this->groupAliases[$group];
    }
    else if (strpos($group, '\\') === false)
    {
      return $this->reflClass->getNamespaceName() . '\\' . $group;
    }
    else
    {
      return ltrim($group, '\\');
    }
  }

  // TODO: testen
  public function resolveGroupNames(array $groups)
  {
    foreach ($groups as $key => $group)
    {
      $groups[$key] = $this->resolveGroupName($group);
    }

    return $groups;
  }

  public function isDefaultGroup()
  {
    return $this->name == self::DEFAULT_GROUP;
  }

  public function setGroupSequence(array $groups)
  {
    $this->groupSequence = $groups;
  }

  public function hasGroupSequence()
  {
    return count($this->groupSequence) > 0;
  }

  public function getGroupSequence()
  {
    return $this->groupSequence;
  }
}