<?php

namespace Symfony\Components\Form\Field;

use Symfony\Components\Form\Field;
use Symfony\Components\Form\Renderer\InputCheckboxRenderer;
use Symfony\Components\Form\ValueTransformer\BooleanToStringTransformer;

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
 * @author Bernhard Schussek <bernhard.schussek@symfony-project.com>
 */
class CheckboxField extends Field
{
  /**
   * {@inheritDoc}
   */
  protected function configure()
  {
    $rendererOptions = array();

    if (isset($options['value']))
    {
      $rendererOptions['value'] = $options['value'];
    }

    $this->setRenderer(new InputCheckboxRenderer($rendererOptions));
    $this->setValueTransformer(new BooleanToStringTransformer());
  }
}