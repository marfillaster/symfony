<?php

namespace Symfony\Components\Form\Field;

use Symfony\Components\Form\FormField;
use Symfony\Components\Form\Renderer\InputCheckboxRenderer;
use Symfony\Components\Form\ValueTransformer\BooleanValueTransformer;

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * A checkbox field for selecting boolean values.
 *
 * @package    symfony
 * @subpackage form/field
 */
class CheckboxField extends FormField
{
  /**
   * Configures the field.
   *
   * Available options:
   *
   *  * value: see InputCheckboxRenderer
   *
   * @param array $options Options for this field
   */
  protected function configure(array $options = array())
  {
    $this->setDefault(false);
    $this->setRenderer(new InputCheckboxRenderer());
    $this->setValueTransformer(new BooleanValueTransformer());

    if (isset($options['value']))
    {
      $this->getRenderer()->setOption('value', $options['value']);
    }
  }
}