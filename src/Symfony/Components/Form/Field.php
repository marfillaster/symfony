<?php

namespace Symfony\Components\Form;

use Symfony\Components\Form\Exception\NotBoundException;
use Symfony\Components\Form\Exception\NotValidException;
use Symfony\Components\Form\Exception\InvalidConfigurationException;

use Symfony\Components\Form\Renderer\RendererInterface;
use Symfony\Components\Form\ValueTransformer\ValueTransformerInterface;

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
 * Field represents a widget bind to a name and a value.
 *
 * @package    symfony
 * @subpackage form
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: Field.php 244 2010-01-31 19:47:33Z bernhard $
 */
class Field extends BaseField
{
  private
    $valueTransformer   = null;

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
    else
    {
      return $this->transform($this->getData());
    }
  }

  /**
   * Binds POST data to the field, transforms and validates it.
   *
   * @param  string|array $taintedData  The POST data
   * @return boolean                    Whether the form is valid
   */
  public function bind($taintedData)
  {
    $this->taintedData = (string)$taintedData;

    $this->injectLocaleAndTranslator($this->valueTransformer);

    try
    {
      parent::bind($this->processData($this->reverseTransform($this->taintedData)));
    }
    catch (ValueTransformerException $e)
    {
      // TODO better text
      // TESTME
      $this->addError('invalid (localized)');
    }
  }

  /**
   * Processes the bound reverse-transformed data.
   *
   * This method can be overridden if you want to modify the data entered
   * by the user. Note that the data is already in reverse transformed format.
   *
   * This method will not be called if reverse transformation fails.
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
    return $this->getRenderer()->needsMultipartForm();
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
    return $this->getRenderer()->isHidden();
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
}
