<?php

namespace Symfony\Components\Validator\Constraints;

use Symfony\Components\Validator\Constraint;
use Symfony\Components\Validator\ConstraintValidator;

class UrlValidator extends ConstraintValidator
{
  const PATTERN = '~^
      (%s)://                                 # protocol
      (
        ([a-z0-9-]+\.)+[a-z]{2,6}             # a domain name
          |                                   #  or
        \d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}    # a IP address
      )
      (:[0-9]+)?                              # a port (optional)
      (/?|/\S+)                               # a /, nothing or a / with something
    $~ix';

  public function isValid($value, Constraint $constraint)
  {
    $pattern = sprintf(self::PATTERN, implode('|', $constraint->protocols));

    if (!preg_match($pattern, $value))
    {
      $this->setMessage($constraint->message, array('value' => $value));

      return false;
    }

    return true;
  }
}