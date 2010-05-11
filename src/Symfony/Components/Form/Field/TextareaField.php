<?php

namespace Symfony\Components\Form\Field;

use Symfony\Components\Form\Field;
use Symfony\Components\Form\Renderer\TextareaRenderer;

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * A textarea field
 *
 * @author Bernhard Schussek <bernhard.schussek@symfony-project.com>
 */
class TextareaField extends Field
{
  /**
   * {@inheritDoc}
   */
  protected function configure()
  {
    $this->setRenderer(new TextareaRenderer());
  }
}
