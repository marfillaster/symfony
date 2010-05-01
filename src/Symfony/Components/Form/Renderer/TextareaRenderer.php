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
 * TextareaRenderer represents an HTML textarea tag.
 */
class TextareaRenderer extends BaseRenderer
{
  /**
   * {@inheritDoc}
   */
  protected function configure()
  {
    $this->setAttribute('rows', 4);
    $this->setAttribute('cols', 30);
  }

  /**
   * {@inheritDoc}
   */
  public function render(FieldInterface $field, array $attributes = array())
  {
    $attributes['id'] = $field->getId();
    $attributes['name'] = $field->getName();

    return $this->renderContentTag('textarea', $this->escapeOnce($field->getDisplayedData()), $attributes);
  }
}
