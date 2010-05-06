<?php

namespace Symfony\Components\Form;

use Symfony\Components\Form\Renderer\RendererInterface;

use Symfony\Components\Form\Exception\NotBoundException;
use Symfony\Components\Form\Exception\NotValidException;
use Symfony\Components\Form\Exception\InvalidConfigurationException;
use Symfony\Components\Form\Exception\NotInitializedException;
use Symfony\Components\Form\Exception\MissingOptionsException;
use Symfony\Components\Form\Exception\InvalidOptionsException;

use Symfony\Components\Validator\ValidatorErrorSchema;

use Symfony\Components\I18N\Localizable;
use Symfony\Components\I18N\Translatable;
use Symfony\Components\I18N\TranslatorInterface;

abstract class BaseField implements FieldInterface, Localizable, Translatable
{
  protected
    $options            = array(),
    $locale             = null,
    $translator         = null;

  private
    $knownOptions       = array(),
    $requiredOptions    = array(),
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

    // check option names
    if ($diff = array_diff_key($this->options, $this->knownOptions))
    {
      throw new InvalidOptionsException(sprintf('%s does not support the following options: "%s".', get_class($this), implode('", "', array_keys($diff))), array_keys($diff));
    }

    // check required options
    if ($diff = array_diff_key($this->requiredOptions, $this->options))
    {
      throw new MissingOptionsException(sprintf('%s requires the following options: \'%s\'.', get_class($this), implode('", "', array_keys($diff))), array_keys($diff));
    }
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
   * Gets an option value.
   *
   * @param  string $name  The option name
    *
   * @return mixed  The option value
   */
  protected function getOption($name)
  {
    return isset($this->options[$name]) ? $this->options[$name] : null;
  }

  /**
   * Adds a new option value with a default value.
   *
   * @param string $name   The option name
   * @param mixed  $value  The default value
   *
   * @return BaseRenderer The current renderer instance
   */
  protected function addOption($name, $value = null)
  {
    $this->knownOptions[$name] = true;

    if (!array_key_exists($name, $this->options))
    {
      $this->options[$name] = $value;
    }

    return $this;
  }

  /**
   * Changes an option value.
   *
   * @param string $name   The option name
   * @param mixed  $value  The value
   *
   * @return BaseRenderer The current renderer instance
   *
   * @throws \InvalidArgumentException when a option is not supported
   */
  protected function setOption($name, $value)
  {
    if (!in_array($name, array_merge(array_keys($this->options), $this->requiredOptions)))
    {
      throw new \InvalidArgumentException(sprintf('%s does not support the following option: \'%s\'.', get_class($this), $name));
    }

    $this->options[$name] = $value;

    return $this;
  }

  /**
   * Returns true if the option exists.
   *
   * @param  string $name  The option name
   *
   * @return bool true if the option exists, false otherwise
   */
  protected function hasOption($name)
  {
    return isset($this->options[$name]);
  }

  /**
   * Adds a required option.
   *
   * @param string $name  The option name
   *
   * @return BaseRenderer The current renderer instance
   */
  protected function addRequiredOption($name)
  {
    $this->knownOptions[$name] = true;
    $this->requiredOptions[$name] = true;

    return $this;
  }

  /**
   * Returns the key of this field.
   *
   * @see FieldInterface
   */
  public function getKey()
  {
    return $this->key;
  }

  /**
   * Returns the name of the field.
   *
   * @see FieldInterface
   */
  public function getName()
  {
    return is_null($this->parent) ? $this->key : $this->parent->getName().'['.$this->key.']';
  }

  /**
   * Returns the ID of the field.
   *
   * @see FieldInterface
   */
  public function getId()
  {
    return is_null($this->parent) ? $this->key : $this->parent->getId().'_'.$this->key;
  }

  /**
   * Sets whether this field is required to be filled out when submitted.
   *
   * @see FieldInterface
   */
  public function setRequired($required)
  {
    $this->required = $required;
  }

  /**
   * Returns whether the field is required to be filled out.
   *
   * @see FieldInterface
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
   * @param FieldInterface $parent  The parent field
   */
  public function setParent(FieldInterface $parent)
  {
    $this->parent = $parent;
  }

  /**
   * Returns the parent field.
   *
   * @return FieldInterface  The parent field
   */
  public function getParent()
  {
    return $this->parent;
  }

  /**
   * Updates the field with default data
   *
   * @see FieldInterface
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
   * @see FieldInterface
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
   * @see FieldInterface
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