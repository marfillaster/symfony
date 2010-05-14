<?php

namespace Symfony\Components\Form\Field;

use Symfony\Components\Form\Field;
use Symfony\Components\Form\Renderer\InputTextRenderer;

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * A text input field.
 *
 * @author Bernhard Schussek <bernhard.schussek@symfony-project.com>
 */
class TextField extends Field
{
  /**
   * {@inheritDoc}
   */
  protected function configure()
  {
    parent::configure();

    $this->addOption('max_length');

    $this->setRenderer(new InputTextRenderer(array(
      'max_length' => $this->getOption('max_length'),
    )));
  }
}