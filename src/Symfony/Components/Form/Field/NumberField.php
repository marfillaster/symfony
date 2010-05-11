<?php

namespace Symfony\Components\Form\Field;

use Symfony\Components\Form\Field;
use Symfony\Components\Form\Renderer\InputTextRenderer;
use Symfony\Components\Form\ValueTransformer\NumberToStringTransformer;

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * A localized field for entering numbers.
 *
 * @author Bernhard Schussek <bernhard.schussek@symfony-project.com>
 */
class NumberField extends Field
{
  /**
   * {@inheritDoc}
   */
  protected function configure()
  {
    $this->addOption('min');
    $this->addOption('max');
    $this->addOption('precision');
    $this->addOption('grouping', false);
    $this->addOption('locale', \Locale::getDefault());

    $this->setRenderer(new InputTextRenderer());
    $this->setValueTransformer(new NumberToStringTransformer(array(
      'precision' => $this->getOption('precision'),
      'grouping' => $this->getOption('grouping'),
      'locale' => $this->getOption('locale'),
    )));
  }
}