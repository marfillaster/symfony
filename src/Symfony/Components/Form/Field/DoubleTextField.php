<?php

namespace Symfony\Components\Form\Field;

use Symfony\Components\Form\FieldGroup;

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * A field for double input of text values
 *
 * @author Bernhard Schussek <bernhard.schussek@symfony-project.com>
 */
class DoubleTextField extends FieldGroup
{
  /**
   * {@inheritDoc}
   */
  protected function configure()
  {
    $this->add(new TextField('first'));
    $this->add(new TextField('second'));
  }

  /**
   * Returns whether both entered values are equal
   *
   * @return bool
   */
  public function isValuesEqual()
  {
    return $this->get('first')->getData() === $this->get('second')->getData();
  }

  /**
   * Return only value of first password field.
   *
   * @return string The password.
   */
  public function getData()
  {
    if ($this->isBound() && $this->isValid())
    {
      return $this->get('first')->getData();
    }

    return null;
  }
}
