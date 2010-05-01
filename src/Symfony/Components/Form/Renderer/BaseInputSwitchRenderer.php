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
 * Renders a field to a HTML input[type=checkbox] tag.
 */
abstract class BaseInputSwitchRenderer extends BaseInputRenderer
{
  /**
   * {@inheritDoc}
   */
  protected function configure()
  {
    parent::configure();

    $this->addOption('value', null);
  }

  /**
   * {@inheritDoc}
   */
  public function render(FieldInterface $field, array $attributes = array())
  {
    $attributes['value'] = $this->getOption('value');

    if ((string)$field->getDisplayedData() !== '' && $field->getDisplayedData() !== 0)
    {
      $attributes['checked'] = 'checked';
    }

    return parent::render($field, $attributes);
  }
}
