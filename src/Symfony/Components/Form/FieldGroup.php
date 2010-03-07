<?php

namespace Symfony\Components\Form;

use Symfony\Components\Form\Exception\AlreadyBoundException;
use Symfony\Components\Form\Exception\UnexpectedTypeException;
use Symfony\Components\Form\Exception\InvalidPropertyException;
use Symfony\Components\Form\Exception\PropertyAccessDeniedException;

use Symfony\Components\Form\Renderer\RendererInterface;
use Symfony\Components\Form\Renderer\Html\GroupRenderer;

use Symfony\Components\Validator\ValidatorInterface;

use Symfony\Components\I18N\Localizable;
use Symfony\Components\I18N\Translatable;
use Symfony\Components\I18N\TranslatorInterface;

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * FieldGroup represents an array of widgets bind to names and values.
 *
 * @package    symfony
 * @subpackage form
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: FieldGroup.php 247 2010-02-01 09:24:55Z bernhard $
 */
class FieldGroup extends BaseField implements \ArrayAccess, \IteratorAggregate, \Countable
{
  // $fields and $merged musst be kept synchronized => private
  private $fields = array();
  private $merged = array();

  protected $class = null;

  public function __construct($key, array $options = array())
  {
//    $this->setRenderer(new GroupRenderer());

    parent::__construct($key, $options);
  }

  public function __clone()
  {
    // should we clone renderer, translator and validators as well?
    // + error schema
    // would not be necessary, if those have a final state i.e. cannot be
    // changed from outside after construction

    foreach ($this->fields as $name => $field)
    {
      $this->fields[$name] = clone $field;
    }
  }

  protected function addField(FieldInterface $field)
  {
    if ($this->isBound())
    {
      throw new AlreadyBoundException('You cannot add fields after binding a form');
    }

    $this->fields[$field->getKey()] = $field;

    $field->setParent($this);

    if ($field instanceof Localizable)
    {
      $field->setLocale($this->locale);
    }

    if ($field instanceof Translatable && !is_null($this->translator))
    {
      $field->setTranslator($this->translator);
    }
  }

  /**
   * Adds a new field to this group. A field must have a unique name within
   * the group. Otherwise the existing field is overwritten.
   *
   * If you add a nested group, this group should also be represented in the
   * object hierarchy. If you want to add a group that operates on the same
   * hierarchy level, use merge().
   *
   * <code>
   * class Entity
   * {
   *   public $location;
   * }
   *
   * class Location
   * {
   *   public $longitude;
   *   public $latitude;
   * }
   *
   * $entity = new Entity();
   * $entity->location = new Location();
   *
   * $form = new Form('entity', $entity, $validator);
   *
   * $locationGroup = new FieldGroup('location');
   * $locationGroup->add(new TextField('longitude'));
   * $locationGroup->add(new TextField('latitude'));
   *
   * $form->add($locationGroup);
   * </code>
   *
   * @param FieldInterface $field
   */
  public function add(FieldInterface $field)
  {
    $this->addField($field);

    $this->merged[$field->getKey()] = false;

    if (!is_null($this->getData()))
    {
      $field->initialize($this->readElement($this->getData(), $field->getKey()));
    }

    return $field;
  }

  /**
   * Merges a field group into this group. The group must have a unique name
   * within the group. Otherwise the existing field is overwritten.
   *
   * Contrary to added groups, merged groups operate on the same object as
   * the group they are merged into.
   *
   * <code>
   * class Entity
   * {
   *   public $longitude;
   *   public $latitude;
   * }
   *
   * $entity = new Entity();
   *
   * $form = new Form('entity', $entity, $validator);
   *
   * $locationGroup = new FieldGroup('location');
   * $locationGroup->add(new TextField('longitude'));
   * $locationGroup->add(new TextField('latitude'));
   *
   * $form->merge($locationGroup);
   * </code>
   *
   * @param FieldGroup $group
   */
  public function merge(FieldGroup $group)
  {
    if ($group->isBound())
    {
      throw new AlreadyBoundException('A bound form group cannot be merged');
    }

    $this->addField($group);

    $this->merged[$group->getKey()] = true;

    if (!is_null($this->getData()))
    {
      $group->initialize($this->getData());
    }

    return $this;
  }

  /**
   * Removes the field with the given key.
   *
   * @param string $key
   */
  public function remove($key)
  {
    unset($this->fields[$key]);
    unset($this->merged[$key]);
  }

  /**
   * Returns whether a field with the given key exists.
   *
   * @param  string $key
   * @return boolean
   */
  public function has($key, $includeMerged = false)
  {
    if (!$includeMerged)
    {
      return isset($this->fields[$key]);
    }
    else if (isset($this->fields[$key]) && !$this->merged[$key])
    {
      return true;
    }
    else
    {
      foreach ($this->merged as $fieldName => $isMerged)
      {
        if ($isMerged && $this->fields[$fieldName]->has($key, true))
        {
          return true;
        }
      }
    }

    return false;
  }

  /**
   * Returns the field with the given key.
   *
   * @param  string $key
   * @return FieldInterface
   */
  public function get($key, $includeMerged = false)
  {
    if (!$includeMerged)
    {
      if (isset($this->fields[$key]))
      {
        return $this->fields[$key];
      }
    }
    else if (isset($this->fields[$key]) && !$this->merged[$key])
    {
      return $this->fields[$key];
    }
    else
    {
      foreach ($this->merged as $fieldName => $isMerged)
      {
        if ($isMerged && $this->fields[$fieldName]->has($key, true))
        {
          return $this->fields[$fieldName]->get($key, true);
        }
      }
    }

    throw new \InvalidArgumentException(sprintf('Field "%s" does not exist.', $key));
  }

  /**
   * Initializes the field group with an object to operate on
   *
   * @see FieldInterface
   */
  public function initialize($data)
  {
    if (!is_array($data) && !is_object($data))
    {
      throw new UnexpectedTypeException('Groups must be initialized with an array or an object');
    }

    parent::initialize($data);

    $this->class = is_object($data) ? new \ReflectionClass($data) : null;

    foreach ($this->fields as $key => $field)
    {
      $field->initialize($this->merged[$key] ? $data : $this->readElement($data, $key));
    }
  }

  /**
   * Reads an element from the given data
   *
   * @param  mixed  $data     The data to read from (array or object)
   * @param  string $element  The element to read
   * @return mixed  $value    The value of the element
   */
  protected function readElement($data, $element)
  {
    if (is_object($data))
    {
      $getter = 'get'.ucfirst($element);
      $isser = 'is'.ucfirst($element);

      if ($this->class->hasMethod($getter))
      {
        if (!$this->class->getMethod($getter)->isPublic())
        {
          throw new PropertyAccessDeniedException(sprintf('Method "%s()" is not public in class "%s"', $getter, $this->class->getName()));
        }

        return $data->$getter();
      }
      else if ($this->class->hasMethod($isser))
      {
        if (!$this->class->getMethod($isser)->isPublic())
        {
          throw new PropertyAccessDeniedException(sprintf('Method "%s()" is not public in class "%s"', $isser, $this->class->getName()));
        }

        return $data->$isser();
      }
      else if ($this->class->hasProperty($element))
      {
        if (!$this->class->getProperty($element)->isPublic())
        {
          throw new PropertyAccessDeniedException(sprintf('Property "%s" is not public in class "%s". Maybe you should create the method "get%s()" or "is%s()"?', $element, $this->class->getName(), ucfirst($element), ucfirst($element)));
        }

        return $data->$element;
      }
      else
      {
        throw new InvalidPropertyException(sprintf('Neither property "%s" nor method "%s()" nor method "%s()" exists in class "%s"', $element, $getter, $isser, $this->class->getName()));
      }
    }
    else
    {
      return isset($data[$element]) ? $data[$element] : null;
    }
  }

  /**
   * Updates the given element with the given value.
   *
   * @param mixed  $data      The data to update
   * @param string $property  The property to update
   * @param mixed  $value     The new value for the field
   */
  protected function updateElement(&$data, $element, $value)
  {
    if (is_object($data))
    {
      $setter = 'set'.ucfirst($element);

      if ($this->class->hasMethod($setter))
      {
        if (!$this->class->getMethod($setter)->isPublic())
        {
          throw new PropertyAccessDeniedException(sprintf('Method "%s()" is not public in class "%s"', $setter, $this->class->getName()));
        }

        $data->$setter($value);
      }
      else if ($this->class->hasProperty($element))
      {
        if (!$this->class->getProperty($element)->isPublic())
        {
          throw new PropertyAccessDeniedException(sprintf('Property "%s" is not public in class "%s". Maybe you should create the method "set%s()"?', $element, $this->class->getName(), ucfirst($element)));
        }

        $data->$element = $value;
      }
      else
      {
        throw new InvalidPropertyException(sprintf('Neither element "%s" nor method "%s()" exists in class "%s"', $element, $setter, $this->class->getName()));
      }
    }
    else
    {
      $data[$element] = $value;
    }
  }

  /**
   * Alias for getData()
   *
   * @see getData()
   */
  public function getObject()
  {
    return $this->getData();
  }

  /**
   * Returns the data of the field as it is displayed to the user.
   *
   * @see FieldInterface
   */
  public function getDisplayedData()
  {
    $values = array();

    foreach ($this->fields as $key => $field)
    {
      $values[$key] = $field->getDisplayedData();
    }

    return $values;
  }

  /**
   * Binds POST data to the field, transforms and validates it.
   *
   * @param  string|array $taintedData  The POST data
   * @return boolean                    Whether the form is valid
   */
  public function bind($taintedData)
  {
    if (is_null($taintedData))
    {
      $taintedData = array();
    }

    if (!is_array($taintedData))
    {
      throw new UnexpectedTypeException('You must pass an array parameter to the bind() method');
    }

    foreach ($this->fields as $key => $field)
    {
      if (!isset($taintedData[$key]))
      {
        $taintedData[$key] = null;
      }
    }

    foreach ($taintedData as $key => $value)
    {
      if ($this->has($key))
      {
        $this->fields[$key]->bind($value);
      }
    }

    $data = $this->getData();

    foreach ($this->fields as $key => $field)
    {
      if (!$this->merged[$key])
      {
        $this->updateElement($data, $key, $field->getData());
      }
    }

    // resets the errors
    parent::bind($data);

    foreach ($taintedData as $key => $value)
    {
      if (!$this->has($key))
      {
        // TODO: can probably be moved to the new validation engine too
        $this->addError('extra field %field%', array('%field%' => $key));
      }
    }
  }

  /**
   * Returns whether the field is valid.
   *
   * @return boolean
   */
  public function isValid()
  {
    if (!parent::isValid())
    {
      return false;
    }

    foreach ($this->fields as $field)
    {
      if (!$field->isValid())
      {
        return false;
      }
    }

    return true;
  }

  /**
   * Returns whether the field requires a multipart form.
   *
   * @return boolean
   */
  public function isMultipart()
  {
    foreach ($this->fields as $field)
    {
      if ($field->isMultipart())
      {
        return true;
      }
    }

    return false;
  }

  /**
   * Gets an option value.
   *
   * @param string $name    The option name
   * @param mixed  $default The default value (null by default)
   *
   * @param mixed  The default value
   */
  public function getOption($name, $default = null)
  {
    return isset($this->options[$name]) ? $this->options[$name] : $default;
  }

  // DEPRECATED
  public function renderError()
  {
//    if (null === $this->parent)
//    {
//      throw new \LogicException(sprintf('Unable to render the error for "%s".', $this->key));
//    }
//
//    $error = $this->getWidget() instanceof WidgetSchema ? $this->getWidget()->getGlobalErrors($this->errorSchema) : $this->errorSchema;
//
//    // FIXME formatErrorsForRow() expects NULL when no error should be rendered
//    if (count($error) == 0)
//    {
//      $error = null;
//    }
//
//    return $this->getParentWidget()->getFormFormatter()->formatErrorsForRow($error);
  }

  /**
   * Renders hidden form fields.
   *
   * @param boolean $recursive False will prevent hidden fields from embedded forms from rendering
   *
   * @return string
   */
  public function renderHiddenFields($recursive = true)
  {
    $output = '';

    foreach ($this->getHiddenFields($recursive) as $field)
    {
      $output .= $field->render();
    }

    return $output;
  }

  /**
   * Returns an array of hidden fields from the current schema.
   *
   * @param boolean $recursive Whether to recur through embedded schemas
   *
   * @return array
   */
  public function getHiddenFields($recursive = true)
  {
    $fields = array();

    foreach ($this->fields as $field)
    {
      if ($field instanceof FieldGroup)
      {
        if ($recursive)
        {
          $fields = array_merge($fields, $field->getHiddenFields($recursive));
        }
      }
      else if ($field->isHidden())
      {
        $fields[] = $field;
      }
    }

    return $fields;
  }

  /**
   * Returns true if the bound field exists (implements the \ArrayAccess interface).
   *
   * @param string $key The key of the bound field
   *
   * @return Boolean true if the widget exists, false otherwise
   */
  public function offsetExists($key)
  {
    return $this->has($key);
  }

  /**
   * Returns the form field associated with the name (implements the \ArrayAccess interface).
   *
   * @param string $key The offset of the value to get
   *
   * @return Field A form field instance
   */
  public function offsetGet($key)
  {
    return $this->get($key);
  }

  /**
   * Throws an exception saying that values cannot be set (implements the \ArrayAccess interface).
   *
   * @param string $offset (ignored)
   * @param string $value (ignored)
   *
   * @throws \LogicException
   */
  public function offsetSet($key, $field)
  {
    throw new \LogicException('Use the method add() to add fields');
  }

  /**
   * Throws an exception saying that values cannot be unset (implements the \ArrayAccess interface).
   *
   * @param string $key
   *
   * @throws \LogicException
   */
  public function offsetUnset($key)
  {
    return $this->remove($key);
  }

  /**
   * Returns the iterator for this group.
   *
   * @return \ArrayIterator
   */
  public function getIterator()
  {
    return new \ArrayIterator($this->fields);
  }

  /**
   * Returns the number of form fields (implements the \Countable interface).
   *
   * @return integer The number of embedded form fields
   */
  public function count()
  {
    return count($this->fields);
  }

  /**
   * Sets the locale of this field.
   *
   * @see Localizable
   */
  public function setLocale($locale)
  {
    parent::setLocale($locale);

    foreach ($this->fields as $field)
    {
      if ($field instanceof Localizable)
      {
        $field->setLocale($locale);
      }
    }
  }

  /**
   * Sets the translator of this field.
   *
   * @see Translatable
   */
  public function setTranslator(TranslatorInterface $translator)
  {
    parent::setTranslator($translator);

    foreach ($this->fields as $field)
    {
      if ($field instanceof Translatable)
      {
        $field->setTranslator($translator);
      }
    }
  }

  /**
   * Sets the charset of the field
   *
   * @see FieldInterface
   */
  public function setCharset($charset)
  {
    parent::setCharset($charset);

    // TESTME
    foreach ($this->fields as $field)
    {
      $field->setCharset($charset);
    }
  }
}
