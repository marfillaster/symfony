<?php

namespace Symfony\Components\Validator\Validators;

class NotNullValidator extends ConstraintValidator
{
  protected function configure()
  {
    $this->addOption('message', 'Value should not be null');
  }

  public function validate($value)
  {
    if (is_null($value))
    {
      $this->setMessage($this->getOption('message'));

      return false;
    }

    return true;
  }
}