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
 * PassValidator is an identity validator. It simply returns the value unmodified.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: PassValidator.php 159 2010-01-24 12:06:04Z flo $
 */
class PassValidator extends BaseValidator
{
  
  public function validate($value)
  {
    $this->doValidate($value);
  }
  
  /**
   * Allways validates.
   *
   * @param  mixed $value   The value that should be validated
   */
  public function doValidate($value)
  {
  }
}