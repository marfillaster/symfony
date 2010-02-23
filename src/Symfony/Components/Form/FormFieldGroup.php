<?php

namespace Symfony\Components\Form;

use Symfony\Components\Form\Exception\AlreadyBoundException;

use Symfony\Components\Form\Renderer\RendererInterface;
use Symfony\Components\Form\Renderer\Html\GroupRenderer;

use Symfony\Components\Validator\ValidatorInterface;
use Symfony\Components\Validator\AndValidator;
use Symfony\Components\Validator\ValidatorError;
use Symfony\Components\Validator\ValidatorErrorSchema;
use Symfony\Components\Validator\PassValidator;

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
 * FormFieldGroup represents an array of widgets bind to names and values.
 *
 * @package    symfony
 * @subpackage form
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: FormFieldGroup.php 247 2010-02-01 09:24:55Z bernhard $
 */
class FormFieldGroup extends BaseFormField implements \ArrayAccess, \IteratorAggregate, \Countable
{
  protected
    $fields         = array(),
    $preValidator   = null,
    $postValidator  = null;

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

  /**
   * Adds a new field to this group. A field must have a unique name within
   * the group. Otherwise the existing field is overwritten.
   *
   * @param FormFieldInterface $field
   */
  public function add(FormFieldInterface $field)
  {
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

    return $field;
  }

  /**
   * Removes the field with the given key.
   *
   * @param string $key
   */
  public function remove($key)
  {
    unset($this->fields[$key]);
  }

  /**
   * Returns whether a field with the given key exists.
   *
   * @param  string $key
   * @return boolean
   */
  public function has($key)
  {
    return isset($this->fields[$key]);
  }

  /**
   * Returns the field with the given key.
   *
   * @param  string $key
   * @return FormFieldInterface
   */
  public function get($key)
  {
    if (!$this->has($key))
    {
      throw new \InvalidArgumentException(sprintf('Field "%s" does not exist.', $key));
    }

    return $this->fields[$key];
  }

  public function merge(FormFieldGroup $group)
  {
    if ($this->isBound() || $group->isBound())
    {
      throw new AlreadyBoundException('A bound form group cannot be merged');
    }

    // FIXME remove CSRF token

    foreach ($group as $field)
    {
      $this->add($field);
    }

    if (!is_null($group->preValidator))
    {
      $this->mergePreValidator($group->preValidator);
    }

    if (!is_null($group->postValidator))
    {
      $this->mergePostValidator($group->postValidator);
    }
  }

  /**
   * Merges a validator with the current pre validators.
   *
   * @param ValidatorInterface $validator A validator to be merged
   */
  private function mergePreValidator(ValidatorInterface $validator)
  {
    if (is_null($this->preValidator))
    {
      $this->preValidator = $validator;
    }
    else
    {
      $this->preValidator = new AndValidator(array(
        $this->preValidator,
        $validator,
      ));
    }
  }

  /**
   * Merges a validator with the current post validators.
   *
   * @param ValidatorInterface $validator A validator to be merged
   */
  private function mergePostValidator(ValidatorInterface $validator)
  {
    if (is_null($this->postValidator))
    {
      $this->postValidator = $validator;
    }
    else
    {
      $this->postValidator = new AndValidator(array(
        $this->postValidator,
        $validator,
      ));
    }
  }

  /**
   * Sets the field's default data.
   *
   * @see FormFieldInterface
   */
  public function setDefault($data)
  {
    if (!is_array($data))
    {
      throw new \InvalidArgumentException('The default data must be an array');
    }

    foreach ($data as $name => $value)
    {
      $this->get($name)->setDefault($value);
    }
  }

  /**
   * Returns the normalized data of the field.
   *
   * @see FormFieldInterface
   */
  public function getData()
  {
    $values = array();

    if ($this->isBound() && $this->isValid())
    {
      foreach ($this->fields as $key => $field)
      {
        $values[$key] = $field->getData();
      }
    }

    return $values;
  }

  /**
   * Returns the data of the field as it is displayed to the user.
   *
   * @see FormFieldInterface
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
      throw new \InvalidArgumentException('You must pass an array parameter to the bind() method of the FormFieldGroup');
    }

    parent::bind($taintedData);

    $this->errorSchema = new ValidatorErrorSchema();

    foreach ($this->fields as $key => $field)
    {
      if (!isset($taintedData[$key]))
      {
        $taintedData[$key] = null;
      }
    }

    $this->preValidate($taintedData);

    $fieldsValid = true;
    foreach ($taintedData as $key => $value)
    {
      if (!$this->has($key))
      {
        $this->errorSchema->addError(new ValidatorError('extra_fields', $key));

        continue;
      }

      $fieldsValid = $this->fields[$key]->bind($value) && $fieldsValid;
    }

    $this->postValidate($this->getData());

    return $fieldsValid && count($this->errorSchema) == 0;
  }

  protected function preValidate($data)
  {
    if (!is_null($this->preValidator))
    {
      $this->injectLocaleAndTranslator($this->preValidator);

      try
      {
        $this->preValidator->validate($data);
      }
      catch (ValidatorErrorSchema $e)
      {
        $this->errorSchema->addErrors($e);
      }
      catch (ValidatorError $e)
      {
        $this->errorSchema->addError($e);
      }
    }
  }

  protected function postValidate($data)
  {
    if (!is_null($this->postValidator))
    {
      $this->injectLocaleAndTranslator($this->postValidator);

      try
      {
        $this->postValidator->validate($data);
      }
      catch (ValidatorErrorSchema $e)
      {
        $this->errorSchema->addErrors($e);
      }
      catch (ValidatorError $e)
      {
        $this->errorSchema->addError($e);
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

  public function setPreValidator(ValidatorInterface $validator)
  {
    $this->preValidator = $validator;
  }

  public function getPreValidator()
  {
    // TESTME
    return $this->preValidator;
  }

  public function setPostValidator(ValidatorInterface $validator)
  {
    $this->postValidator = $validator;
  }

  public function getPostValidator()
  {
    // TESTME
    return $this->postValidator;
  }

  public function getDefault()
  {
    $default = array();

    foreach ($this->fields as $key => $field)
    {
      $default[$key] = $field->getDefault();
    }

    return $default;
  }

  /**
   * Sets an option value.
   *
   * @param string $name  The option name
   * @param mixed  $value The default value
   *
   * @return Form The current form instance
   */
  public function setOption($name, $value)
  {
    $this->options[$name] = $value;

    return $this;
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
      if ($field instanceof FormFieldGroup)
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
   * @return FormField A form field instance
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
   * @see FormFieldInterface
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
