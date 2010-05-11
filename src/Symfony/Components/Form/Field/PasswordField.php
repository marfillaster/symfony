<?php

namespace Symfony\Components\Form\Field;

use Symfony\Components\Form\Renderer\InputPasswordRenderer;

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * A field for entering a password.
 *
 * @author Bernhard Schussek <bernhard.schussek@symfony-project.com>
 */
class PasswordField extends TextField
{
  /**
   * {@inheritDoc}
   */
  protected function configure()
  {
    parent::configure();

    $this->addOption('always_empty', true);

    $this->setRenderer(new InputPasswordRenderer(array(
      'always_empty' => $this->getOption('always_empty'),
    )));
  }
}