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
 * Renders a field to a HTML input[type=password] tag.
 */
class InputPasswordRenderer extends BaseInputRenderer
{
  /**
   * {@inheritDoc}
   */
  protected function configure()
  {
    parent::configure();

    $this->addOption('always_empty', true);
  }

  /**
   * {@inheritDoc}
   */
  public function render(FieldInterface $field, array $attributes = array())
  {
    $attributes['type'] = 'password';

    if (!$this->getOption('always_empty'))
    {
      $attributes['value'] = $field->getDisplayedData();
    }

    return parent::render($field, $attributes);
  }
}
