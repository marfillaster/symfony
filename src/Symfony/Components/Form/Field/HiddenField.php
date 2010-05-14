<?php

namespace Symfony\Components\Form\Field;

use Symfony\Components\Form\Renderer\InputHiddenRenderer;

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * A hidden field
 *
 * @author Bernhard Schussek <bernhard.schussek@symfony-project.com>
 */
class HiddenField extends Field
{
  /**
   * {@inheritDoc}
   */
  protected function configure()
  {
    $this->setRenderer(new InputHiddenRenderer());
  }
}