<?php

namespace Symfony\Components\Validator\Mapping\Loader;

use Symfony\Components\Validator\Exception\MappingException;
use Symfony\Components\Validator\Mapping\ClassMetadata;

class XmlFileLoader extends FileLoader
{
  /**
   * An array of SimpleXMLElement instances
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
      $this->classes = array();
      $xml = $this->parseFile($this->file);

      foreach ($xml->class as $class)
      {
        $this->classes[(string)$class['name']] = $class;
      }
    }

    if (isset($this->classes[$metadata->getClassName()]))
    {
      $xml = $this->classes[$metadata->getClassName()];

      foreach ($this->parseConstraints($xml->constraint) as $constraint)
      {
        $metadata->addConstraint($constraint);
      }

      foreach ($xml->property as $property)
      {
        foreach ($this->parseConstraints($property->constraint) as $constraint)
        {
          $metadata->addPropertyConstraint((string)$property['name'], $constraint);
        }
      }

      foreach ($xml->getter as $getter)
      {
        foreach ($this->parseConstraints($getter->constraint) as $constraint)
        {
          $metadata->addGetterConstraint((string)$getter['property'], $constraint);
        }
      }

      return true;
    }

    return false;
  }

  /**
   * Parses a collection of "constraint" XML nodes
   *
   * @param  SimpleXMLElement $nodes  The XML nodes
   * @return array                    An array of Constraint instances
   */
  protected function parseConstraints(\SimpleXMLElement $nodes)
  {
    $constraints = array();

    foreach ($nodes as $node)
    {
      $className = 'Symfony\\Components\\Validator\\Constraints\\'.$node['name'];
      $attributes = null;

      if (count($node) > 0)
      {
        $attributes = array();

        foreach ($node->value as $value)
        {
          $attributes[] = trim($value);
        }

        foreach ($node->attribute as $attribute)
        {
          $attributeName = (string)$attribute['name'];

          if (count($attribute) > 0)
          {
            $attributes[$attributeName] = array();

            foreach ($attribute->value as $value)
            {
              $attributes[$attributeName][] = trim($value);
            }
          }
          else
          {
            $attributes[$attributeName] = trim($attribute);
          }
        }
      }
      else if (strlen((string)$node) > 0)
      {
        $attributes = trim($node);
      }

      $constraints[] = new $className($attributes);
    }

    return $constraints;
  }

  /**
   * @param  string $file
   * @return SimpleXMLElement
   */
  protected function parseFile($file)
  {
    $dom = new \DOMDocument();
    libxml_use_internal_errors(true);
    if (!$dom->load($file, LIBXML_COMPACT))
    {
      throw new MappingException(implode("\n", $this->getXmlErrors()));
    }
    if (!$dom->schemaValidate(__DIR__.'/schema/dic/constraint-mapping/constraint-mapping-1.0.xsd'))
    {
      throw new MappingException(implode("\n", $this->getXmlErrors()));
    }
    $dom->validateOnParse = true;
    $dom->normalizeDocument();
    libxml_use_internal_errors(false);

    return simplexml_import_dom($dom);
  }

  protected function getXmlErrors()
  {
    $errors = array();
    foreach (libxml_get_errors() as $error)
    {
      $errors[] = sprintf('[%s %s] %s (in %s - line %d, column %d)',
        LIBXML_ERR_WARNING == $error->level ? 'WARNING' : 'ERROR',
        $error->code,
        trim($error->message),
        $error->file ? $error->file : 'n/a',
        $error->line,
        $error->column
      );
    }

    libxml_clear_errors();

    return $errors;
  }
}