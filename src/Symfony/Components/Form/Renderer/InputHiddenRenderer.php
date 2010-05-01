<?php

namespace Symfony\Components\Form\Renderer;

use Symfony\Components\Form\FieldInterface;

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Renders a field to a HTML input[type=hidden] tag.
 */
class InputHiddenRenderer extends BaseInputRenderer
{
  /**
   * {@inheritDoc}
   */
  public function render(FieldInterface $field, array $attributes = array())
  {
    $attributes['type'] = 'hidden';
    $attributes['value'] = $field->getDisplayedData();

    return parent::render($field, $attributes);
  }

  /**
   * {@inheritDoc}
   */
  public function isHidden()
  {
    return true;
  }
}
