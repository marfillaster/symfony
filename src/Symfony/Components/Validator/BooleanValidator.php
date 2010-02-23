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
 * BooleanValidator validates a boolean. It also converts the input value to a valid boolean.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Florian Eckerstorfer <florian@eckerstorfer.org>
 * @version    SVN: $Id: BooleanValidator.php 159 2010-01-24 12:06:04Z flo $
 */
class BooleanValidator extends BaseValidator
{
  
  protected function configure($options = array(), $messages = array())
  {
    $this->setMessage('invalid', 'The field must be a boolean.');
  }
  
  /**
   * Validates whether a value is either true or false.
   *
   * @param  mixed $value   The value that should be validated
   * @throws ValidatorError when the value is not a boolean.
   */
  protected function doValidate($value)
  {
    if (!is_bool($value))
    {
      throw new ValidatorError($this->getMessage('invalid'));
    }
  }
}
