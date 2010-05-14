<?php

namespace Symfony\Components\Validator\Mapping\Loader;

use Symfony\Components\Validator\Exception\MappingException;
use Symfony\Components\Validator\Mapping\ClassMetadata;
use Symfony\Components\Yaml\Yaml;

class YamlFileLoader extends FileLoader
{
  /**
   * An array of YAML class descriptions
   * @val array
   */
  protected $classes = null;

  /**
   * {@inheritDoc}
   */
  public function loadClassMetadata(ClassMetadata $metadata)
  {
    if (is_null($this->classes))
    {
      $this->classes = Yaml::load($this->file);
    }

    // TODO validation

    if (isset($this->classes[$metadata->getClassName()]))
    {
      $yaml = $this->classes[$metadata->getClassName()];

      if (isset($yaml['constraints']))
      {
        foreach ($this->parseConstraints($yaml['constraints']) as $constraint)
        {
          $metadata->addConstraint($constraint);
        }
      }

      if (isset($yaml['properties']))
      {
        foreach ($yaml['properties'] as $property => $constraints)
        {
          foreach ($this->parseConstraints($constraints) as $constraint)
          {
            $metadata->addPropertyConstraint($property, $constraint);
          }
        }
      }

      if (isset($yaml['getters']))
      {
        foreach ($yaml['getters'] as $getter => $constraints)
        {
          foreach ($this->parseConstraints($constraints) as $constraint)
          {
            $metadata->addGetterConstraint($getter, $constraint);
          }
        }
      }

      return true;
    }

    return false;
  }

  /**
   * Parses a collection of "constraint" YAML nodes
   *
   * @param  array $nodes  The YAML nodes
   * @return array         An array of Constraint instances
   */
  protected function parseConstraints(array $nodes)
  {
    $constraints = array();

    foreach ($nodes as $name => $options)
    {
      $className = 'Symfony\\Components\\Validator\\Constraints\\'.$name;

      $constraints[] = new $className($options);
    }

    return $constraints;
  }
}