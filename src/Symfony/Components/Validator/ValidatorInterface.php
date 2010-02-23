<?php

namespace Symfony\Components\Validator;

/**
 * Validates a given value.
 *
 * @author     Bernhard Schussek <bernhard.schussek@symfony-project.com>
 * @version    SVN: $Id: ValidatorInterface.php 138 2010-01-18 22:05:14Z flo $
 */
interface ValidatorInterface
{
  /**
   * Validates the value.
   *
   * @param  mixed $value   The value that should be validated
   * @throws InvalidArgumentException when the argument is not of the
   *                                  expected type
   * @throws ValidatorError when the validation fails
   */
  public function validate($value);

  /**
   * Temporary - until the option "required" is separated
   *
   * @param unknown_type $name
   * @param unknown_type $value
   * @return unknown_type
   */
  public function setOption($name, $value);
}