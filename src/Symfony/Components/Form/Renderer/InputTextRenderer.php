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
 * Renders a field to a HTML input tag.
 */
class InputTextRenderer extends BaseInputRenderer
{
  /**
   * {@inheritDoc}
   */
  protected function configure()
  {
    parent::configure();

    $this->addOption('max_length', false);
  }

  /**
   * {@inheritDoc}
   */
  public function render(FieldInterface $field, array $attributes = array())
  {
    $attributes['value'] = $field->getDisplayedData();
    $attributes['type'] = 'text';

    if ($this->getOption('max_length'))
    {
      $attributes['max_length'] = $this->getOption('max_length');
    }

    return parent::render($field, $attributes);
  }
}
