<?php

namespace Symfony\Components\Validator;

use Symfony\Components\Validator\Exception\InvalidAttributesException;
use Symfony\Components\Validator\Exception\MissingAttributesException;
use Symfony\Components\Validator\Exception\ConstraintDefinitionException;

/**
 * Contains the properties of a constraint definition.
 *
 * A constraint can be defined on a class, an attribute or a getter method.
 * The Constraint class encapsulates all the configuration required for
 * validating this class, attribute or getter result successfully.
 *
 * Constraint instances are immutable and serializable.
 *
 * @author Bernhard Schussek <bernhard.schussek@symfony-project.com>
 */
class Constraint
{
  const DEFAULT_GROUP = 'Default';

  public $groups = self::DEFAULT_GROUP;

  /**
   * Initializes the constraint with attributes
   *
   * You should pass an associative array. The keys should be the names of
   * existing properties in this class. The values should be the value for these
   * properties.
   *
   * Alternatively you can override the method defaultAttribute() to return the
   * name of an existing property. If no associative array is passed, this
   * property is set instead.
   *
   * You can force that certain attributes are set by overriding
   * requiredAttributes() to return the names of these attributes. If any
   * attribute is not set here, an exception is thrown.
   *
   * @param mixed $attributes               The attributes (as associative array)
   *                                        or the value for the default
   *                                        attribute (any other type)
   * @throws InvalidAttributesException     When you pass the names of non-existing
   *                                        attributes
   * @throws MissingAttributesException     When you don't pass any of the attributes
   *                                        returned by requiredAttributes()
   * @throws ConstraintDefinitionException  When you don't pass an associative
   *                                        array, but defaultAttribute() returns
   *                                        NULL
   */
  public function __construct($attributes = null)
  {
    $invalidAttributes = array();
    $missingAttributes = array_flip((array)$this->requiredAttributes());

    if (is_array($attributes) && count($attributes) > 0 && is_string(key($attributes)))
    {
      foreach ($attributes as $attribute => $value)
      {
        if (property_exists($this, $attribute))
        {
          $this->$attribute = $value;
          unset($missingAttributes[$attribute]);
        }
        else
        {
          $invalidAttributes[] = $attribute;
        }
      }
    }
    else if (!is_null($attributes))
    {
      $attribute = $this->defaultAttribute();

      if (is_null($attribute))
      {
        throw new ConstraintDefinitionException(
          sprintf('No default attribute is configured for constraint %s', get_class($this))
        );
      }

      if (property_exists($this, $attribute))
      {
        $this->$attribute = $attributes;
        unset($missingAttributes[$attribute]);
      }
      else
      {
        $invalidAttributes[] = $attribute;
      }
    }

    if (count($invalidAttributes) > 0)
    {
      throw new InvalidAttributesException(
        sprintf('The attributes "%s" do not exist in constraint %s', implode('", "', $invalidAttributes), get_class($this)),
        $invalidAttributes
      );
    }

    if (count($missingAttributes) > 0)
    {
      throw new MissingAttributesException(
        sprintf('The attributes "%s" must be set for constraint %s', implode('", "', $missingAttributes), get_class($this)),
        $missingAttributes
      );
    }
  }

  /**
   * Unsupported operation.
   */
  public function __set($attribute, $value)
  {
    throw new InvalidAttributesException(sprintf('The attribute "%s" does not exist in constraint %s', $attribute, get_class($this)), array($attribute));
  }

  /**
   * Returns the name of the default attribute
   *
   * Override this method to define a default attribute.
   *
   * @return string
   * @see __construct()
   */
  public function defaultAttribute()
  {
    return null;
  }

  /**
   * Returns the name of the required attributes
   *
   * Override this method if you want to define required attributes.
   *
   * @return array
   * @see __construct()
   */
  public function requiredAttributes()
  {
    return array();
  }

  /**
   * Returns the name of the class that validates this constraint
   *
   * By default, this is the fully qualified name of the constraint class
   * suffixed with "Validator". You can override this method to change that
   * behaviour.
   *
   * @return string
   */
  public function validatedBy()
  {
    return get_class($this) . 'Validator';
  }
}