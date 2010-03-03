<?php

namespace Symfony\Components\Form;

use Symfony\Components\Form\Renderer\RendererInterface;

use Symfony\Components\Form\Exception\NotBoundException;
use Symfony\Components\Form\Exception\NotValidException;
use Symfony\Components\Form\Exception\InvalidConfigurationException;
use Symfony\Components\Form\Exception\NotInitializedException;

use Symfony\Components\Validator\ValidatorErrorSchema;

use Symfony\Components\I18N\Localizable;
use Symfony\Components\I18N\Translatable;
use Symfony\Components\I18N\TranslatorInterface;

abstract class BaseFormField implements FormFieldInterface, Localizable, Translatable
{
  protected
    $options            = array(),
    $locale             = null,
    $translator         = null;

  private
    $errors             = array(),
    $key                = '',
    $parent             = null,
    $renderer           = null,
    $bound              = false,
    $required           = true,
    $charset            = 'UTF-8',
    $data               = null,
    $initialized        = false;

  public function __construct($key, array $options = array())
  {
    $this->options = $options;
    $this->key = (string)$key;
    $this->locale = \Locale::getDefault();

    $this->configure();
  }

  /**
   * Returns the string representation of this form field.
   *
   * @return string The rendered field
   */
  public function __toString()
  {
    return $this->render();
  }

  public function __clone()
  {
    // TODO
  }

  protected function configure()
  {
  }

  /**
   * Returns the key of this field.
   *
   * @see FormFieldInterface
   */
  public function getKey()
  {
    return $this->key;
  }

  /**
   * Returns the name of the field.
   *
   * @see FormFieldInterface
   */
  public function getName()
  {
    return is_null($this->parent) ? $this->key : $this->parent->getName().'['.$this->key.']';
  }

  /**
   * Returns the ID of the field.
   *
   * @see FormFieldInterface
   */
  public function getId()
  {
    return is_null($this->parent) ? $this->key : $this->parent->getId().'_'.$this->key;
  }

  /**
   * Sets whether this field is required to be filled out when submitted.
   *
   * @see FormFieldInterface
   */
  public function setRequired($required)
  {
    $this->required = $required;
  }

  /**
   * Returns whether the field is required to be filled out.
   *
   * @see FormFieldInterface
   */
  public function isRequired()
  {
    if (is_null($this->parent) || $this->parent->isRequired())
    {
      return $this->required;
    }
    else
    {
      return false;
    }
  }

  /**
   * Sets the parent field.
   *
   * @param FormFieldInterface $parent  The parent field
   */
  public function setParent(FormFieldInterface $parent)
  {
    $this->parent = $parent;
  }

  /**
   * Returns the parent field.
   *
   * @return FormFieldInterface  The parent field
   */
  public function getParent()
  {
    return $this->parent;
  }

  /**
   * Updates the field with default data
   *
   * @see FormFieldInterface
   */
  public function initialize($data)
  {
    $this->data = $data;
    $this->initialized = true;
  }

  /**
   * Binds POST data to the field, transforms and validates it.
   *
   * @param  string|array $taintedData  The POST data
   * @return boolean                    Whether the form is valid
   * @throws AlreadyBoundException      when the field is already bound
   */
  public function bind($data)
  {
    if (!$this->initialized)
    {
      throw new NotInitializedException('You must initialize the field before binding');
    }

    $this->data = $data;
    $this->bound = true;
    $this->errors = array();
  }

  /**
   * Returns the normalized data of the field.
   *
   * @return mixed  When the field is not bound, the default data is returned.
   *                When the field is bound, the normalized bound data is
   *                returned if the field is valid, null otherwise.
   */
  public function getData()
  {
    return $this->data;
  }

  /**
   * Adds an error to the field.
   *
   * @see FormFieldInterface
   */
  public function addError($message, array $parameters = array())
  {
    if (!is_null($this->translator))
    {
      $message = $this->translator->translate($message, $parameters);
    }
    else
    {
      $message = str_replace(array_keys($parameters), $parameters, $message);
    }

    $this->errors[] = $message;
  }

  /**
   * Returns whether the field is bound.
   *
   * @return boolean  true if the form is bound to input values, false otherwise
   */
  public function isBound()
  {
    return $this->bound;
  }

  /**
   * Returns whether the field is valid.
   *
   * @return boolean
   */
  public function isValid()
  {
    return $this->isBound() ? count($this->errors)==0 : false; // TESTME
  }

  /**
   * Sets the renderer.
   *
   * @param RendererInterface $renderer
   */
  public function setRenderer(RendererInterface $renderer)
  {
    $this->renderer = $renderer;
  }

  /**
   * Returns the current renderer.
   *
   * @return RendererInterface
   */
  public function getRenderer()
  {
    return $this->renderer;
  }

  /**
   * Delegates the rendering of the field to the renderer set.
   *
   * @return string The rendered widget
   */
  public function render(array $attributes = array())
  {
    if (is_null($this->renderer))
    {
      throw new InvalidConfigurationException('A renderer must be set before rendering a field');
    }

    $this->injectLocaleAndTranslator($this->renderer);

    return $this->renderer->render($this, $attributes);
  }

  /**
   * Returns weather there are errors.
   *
   * @return boolean  true if form is bound and not valid
   */
  public function hasErrors()
  {
    return $this->isBound() && !$this->isValid();
  }

  /**
   * Returns all errors
   *
   * @return array  An array of errors that occured during binding
   */
  public function getErrors()
  {
    return $this->errors;
  }

  /**
   * Sets the locale of this field.
   *
   * @see Localizable
   */
  public function setLocale($locale)
  {
    $this->locale = $locale;
  }

  /**
   * Sets the translator of this field.
   *
   * @see Translatable
   */
  public function setTranslator(TranslatorInterface $translator)
  {
    $this->translator = $translator;
  }

  /**
   * Injects the locale and the translator into the given object, if set.
   *
   * The locale is injected only if the object implements Localizable. The
   * translator is injected only if the object implements Translatable.
   *
   * @param object $object
   */
  protected function injectLocaleAndTranslator($object)
  {
    if ($object instanceof Localizable)
    {
      $object->setLocale($this->locale);
    }

    if (!is_null($this->translator) && $object instanceof Translatable)
    {
      $object->setTranslator($this->translator);
    }
  }

  /**
   * Sets the charset of the field
   *
   * @see FormFieldInterface
   */
  public function setCharset($charset)
  {
    $this->charset = $charset;
  }

  /**
   * Returns the charset of the field
   *
   * @return string
   */
  public function getCharset()
  {
    return $this->charset;
  }
}