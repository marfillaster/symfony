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
 * InputRenderer represents an HTML input tag.
 */
abstract class BaseInputRenderer extends BaseRenderer
{
  /**
   * {@inheritDoc}
   */
  public function render(FieldInterface $field, array $attributes = array())
  {
    return $this->renderTag('input', array_merge(array(
      'id' => $field->getId(),
      'name' => $field->getName(),
    ), $attributes));
  }
}
