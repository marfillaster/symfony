<?php

namespace Symfony\Components\Form\Field;

use Symfony\Components\Form\Field;
use Symfony\Components\Form\ValueTransformer\PercentToStringTransformer;

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * A localized field for entering percentage values.
 *
 * @author Bernhard Schussek <bernhard.schussek@symfony-project.com>
 */
class PercentField extends Field
{
  /**
   * {@inheritDoc}
   */
  protected function configure()
  {
    $this->addOption('min');
    $this->addOption('max');
    $this->addOption('precision', 0);
    $this->addOption('locale', \Locale::getDefault());
    $this->addOption('type', PercentToStringTransformer::FRACTIONAL);

    $this->setRenderer(new InputTextRenderer());
    $this->setValueTransformer(new PercentToStringTransformer(array(
      'precision' => $this->getOption('precision'),
      'locale' => $this->getOption('locale'),
      'type' => $this->getOption('type'),
    )));
  }
}