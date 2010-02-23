<?php

namespace Symfony\Components\Validator;

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * ChoiceValidator validates that the value is one of the expected values.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Florian Eckerstorfer <florian@eckerstorfer.org>
 * @version    SVN: $Id: ChoiceValidator.php 249 2010-02-01 11:07:14Z robert $
 */
class ChoiceValidator extends BaseValidator
{
  /**
   * Configures the current validator.
   *
   * Available options:
   *
   *  * choices:  An array of expected values (required)
   *  * multiple: true if the select tag must allow multiple selections
   *  * min:      The minimum number of values that need to be selected (this option is only active if multiple is true)
   *  * max:      The maximum number of values that need to be selected (this option is only active if multiple is true)
   *
   * @param array $options    An array of options
   * @param array $messages   An array of error messages
   *
   * @see BaseValidator
   */
  protected function configure($options = array(), $messages = array())
  {
    $this->addRequiredOption('choices');
    $this->addOption('multiple', false);
    $this->addOption('min');
    $this->addOption('max');

    $this->addMessage('min', 'At least %min% values must be selected (%count% values selected).');
    $this->addMessage('max', 'At most %max% values must be selected (%count% values selected).');
  }
  
  protected function doValidate($value)
  {
    if (is_null($value))
    {
      // not true
      return; 
    }
    
    if ($this->getOption('multiple'))
    {
      $count = 0;
      foreach ($value as $thisValue)
      {
        if (!$this->isChoice($thisValue, $this->getOption('choices')))
        {
          throw new ValidatorError($this->getMessage('invalid'));
        }
        ++$count;
      }
      if (!is_null($this->getOption('min')) && $count < $this->getOption('min'))
      {
        throw new ValidatorError($this->getMessage('min', array('min' => $this->getOption('min'), 'count' => $count)));
      }
      if (!is_null($this->getOption('max')) && $count > $this->getOption('max'))
      {
        throw new ValidatorError($this->getMessage('max', array('max' => $this->getOption('max'), 'count' => $count)));
      }
    }
    elseif (!$this->isChoice($value, $this->getOption('choices')))
    {
      throw new ValidatorError($this->getMessage('invalid'));
    }
  }
  
  protected function isChoice($value, $choices)
  {
    foreach ($choices as $choice)
    {
      if ($value === $choice)
      {
        return true;
      }
    }
    return false;
  }
}
