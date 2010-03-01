<?php

namespace Symfony\Components\Validator\Validators;

class SizeValidator extends ConstraintValidator
{
  protected function configure()
  {
    $this->addOption('min');
    $this->addOption('minMessage', 'Value should not be smaller than %min%');
    $this->addOption('max');
    $this->addOption('maxMessage', 'Value should not be greater than %max%');
  }

  public function validate($value)
  {
    if (!is_null($this->getOption('min')))
    {
      if ($value < $this->getOption('min'))
      {
        $this->setMessage($this->getOption('minMessage'), array('%min%' => $this->getOption('min')));

        return false;
      }
    }

    if (!is_null($this->getOption('max')))
    {
      if ($value > $this->getOption('max'))
      {
        $this->setMessage($this->getOption('maxMessage'), array('%max%' => $this->getOption('max')));

        return false;
      }
    }

    return true;
  }
}