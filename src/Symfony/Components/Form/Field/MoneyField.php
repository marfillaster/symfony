<?php

namespace Symfony\Components\Form\Field;

use Symfony\Components\Form\ValueTransformer\MoneyToStringTransformer;

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * A localized field for entering money values
 *
 * @author Bernhard Schussek <bernhard.schussek@symfony-project.com>
 */
class MoneyField extends NumberField
{
  /**
   * {@inheritDoc}
   */
  protected function configure()
  {
    $this->addOption('divisor');
    $this->addOption('currency'); // TODO: display the currency

    parent::configure();

    $this->setValueTransformer(new MoneyToStringTransformer(array(
      'precision' => $this->getOption('precision'),
      'grouping' => $this->getOption('grouping'),
      'locale' => $this->getOption('locale'),
      'divisor' => $this->getOption('divisor'),
    )));
  }
}