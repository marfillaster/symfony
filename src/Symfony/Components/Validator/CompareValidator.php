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
 * Compares two keys of an array. Requires the given value to be an array.
 *
 * Options:
 *  * "left": The "left" key to compare
 *  * "right": The "right" key to compare
 *  * "operator": The operator to use. Can be one of "==", "!=", "===", "!==", "<", "<=", ">" and ">="
 *
 * @package    symfony
 * @subpackage validator
 * @author	   Florian Eckerstorfer <florian@eckerstorfer.org>
 * @version		
 */
class CompareValidator extends BaseValidator
{
  protected function configure($options = array(), $messages = array())
  {
    $this->addRequiredOption('left');
    $this->addRequiredOption('right');
    $this->addRequiredOption('operator');
  }
  
  /**
   * Compares two keys of an array. Requires the given value to be an array.
   *
   * @param  mixed $value   The value that should be validated
   */
  public function doValidate($value)
  {
    if (!is_array($value))
    {
      throw new \InvalidArgumentException('Value must be an array.');
    }
    if (!isset($value[$this->getOption('left')]) || !isset($value[$this->getOption('right')]))
    {
      throw new \LogicException('Element does not exists in the value array.');
    }
    
    $left   = $value[$this->getOption('left')];
    $right  = $value[$this->getOption('right')];
    $operator = $this->getOption('operator');
    
    switch ($operator)
    {
      case '==':  $expression = $left == $right; break;
      case '!=':  $expression = $left != $right; break;
      case '===': $expression = $left === $right; break;
      case '!==': $expression = $left !== $right; break;
      case '<':   $expression = $left < $right; break;
      case '>':   $expression = $left > $right; break;
      case '<=':  $expression = $left <= $right; break;
      case '>=':  $expression = $left >= $right; break;
      default:
        throw new \LogicException('Invalid operator "' . $this->getOption('operator') . '".');
    }
    
    if (!$expression)
    {
      throw new ValidatorError($this->getMessage('invalid', array('left' => $left, 'right' => $right, 'operator' => $operator)));
    }
  }
}
