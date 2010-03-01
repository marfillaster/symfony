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
  public function validate($object, $groups = 'default');

  public function validateProperty($object, $property, $groups = 'default');

  public function validateValue($class, $property, $value, $groups = 'default');
}