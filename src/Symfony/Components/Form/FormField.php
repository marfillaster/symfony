<?php

namespace Symfony\Components\Form;

use Symfony\Components\Form\Exception\NotBoundException;
use Symfony\Components\Form\Exception\NotValidException;
use Symfony\Components\Form\Exception\InvalidConfigurationException;

use Symfony\Components\Form\Renderer\RendererInterface;

use Symfony\Components\ValueTransformer\ValueTransformerInterface;

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
 * FormField represents a widget bind to a name and a value.
 *
 * @package    symfony
 * @subpackage form
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: FormField.php 244 2010-01-31 19:47:33Z bernhard $
 */
class FormField extends BaseFormField
{
  private
    $valueTransformer   = null,
    $default            = null,
    $data               = null;

  protected
    $taintedData        = null;

  /**
   * Clones this field.
   */
  public function __clone()
  {
    // TODO
  }

  /**
   * Sets the field's default data.
   *
   * @see FormFieldInterface
   */
  public function setDefault($data)
  {
    $this->default = $data;
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
    if (!$this->isBound())
    {
      return $this->default;
    }
    else
    {
      return $this->data;
    }
  }

  /**
   * Returns the data of the field as it is displayed to the user.
   *
   * @return string|array  When the field is not bound, the transformed
   *                       default data is returned. When the field is bound,
   *                       the bound data is returned.
   */
  public function getDisplayedData()
  {
    if ($this->isBound() && !$this->isValid())
    {
      return $this->taintedData;
    }
    else if ($this->isBound())
    {
      return $this->transform($this->data);
    }
    else
    {
      return $this->transform($this->default);
    }
  }

  /**
   * Judges whether the given value is considered empty by the field.
   *
   * If the field is empty, it validates only if it is not required.
   *
   * @param  mixed $value  The reverse transformed value entered by the user
   * @return boolean       Whether the value is considered empty
   */
  protected function isEmpty($value)
  {
    return $value === null || $value === '' || $value === false;
  }

  /**
   * Binds POST data to the field, transforms and validates it.
   *
   * @param  string|array $taintedData  The POST data
   * @return boolean                    Whether the form is valid
   */
  public function bind($taintedData)
  {
    parent::bind($taintedData);

    $this->taintedData = (string)$taintedData;

    $this->injectLocaleAndTranslator($this->valueTransformer);

    try
    {
      $this->data = $this->processData($this->reverseTransform($this->taintedData));
    }
    catch (ValueTransformerException $e)
    {
      // TODO better text
      // TESTME
      $this->errors[] = 'invalid (localized)';
    }

    return false;
  }

  /**
   * Processes the bound reverse-transformed data before validation.
   *
   * This method can be overridden if you want to modify the data entered
   * by the user before it is passed to the validator. Note that the data is
   * already in reverse transformed format.
   *
   * @param  mixed $data
   * @return mixed
   */
  protected function processData($data)
  {
    return $data;
  }

  /**
   * Returns whether the field requires a multipart form.
   *
   * @return boolean
   */
  public function isMultipart()
  {
//    return $this->renderer->needsMultipartForm();
  }

  /**
   * Reverse transforms a value if a value transformer is set.
   *
   * @param  string $value  The value to reverse transform
   * @return mixed
   */
  private function reverseTransform($value)
  {
    if (is_null($this->valueTransformer))
    {
      return $value;
    }
    else
    {
      return $this->valueTransformer->reverseTransform($value);
    }
  }

  /**
   * Transforms the value if a value transformer is set.
   *
   * @param  mixed $value  The value to transform
   * @return string
   */
  private function transform($value)
  {
    if (is_null($this->valueTransformer))
    {
      return $value;
    }
    else
    {
      return $this->valueTransformer->transform($value);
    }
  }

  /**
   * Returns true if the widget is hidden.
   *
   * @return Boolean true if the widget is hidden, false otherwise
   */
  public function isHidden()
  {
//    return $this->renderer->isHidden();
  }

  /**
   * Sets the ValueTransformer.
   *
   * @param ValueTransformerInterface $valueTransformer
   */
  public function setValueTransformer(ValueTransformerInterface $valueTransformer)
  {
    $this->valueTransformer = $valueTransformer;
  }

  /**
   * Returns the ValueTransformer.
   *
   * @return ValueTransformerInterface
   */
  public function getValueTransformer()
  {
    return $this->valueTransformer;
  }

  /**
   * Returns a formatted error list.
   *
   * The formatted list will use the parent widget schema formatter.
   *
   * @return string The formatted error list
   */
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
}
