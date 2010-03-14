<?php

namespace Symfony\Components\Validator\Constraints;

use Symfony\Components\Validator\Engine\Constraint;
use Symfony\Components\Validator\Engine\ConstraintValidator;

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Choice extends Constraint
{
  public $choices = array();
  public $callback;
  public $message = '%value% is not a valid choice';
  public $multiple = false;
  public $min;
  public $minMessage = 'Please select at least %min% choices';
  public $max;
  public $maxMessage = 'Please select no more than %max% choices';
}

/**
 * ChoiceValidator validates that the value is one of the expected values.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Florian Eckerstorfer <florian@eckerstorfer.org>
 * @version    SVN: $Id: ChoiceValidator.php 249 2010-02-01 11:07:14Z robert $
 */
class ChoiceValidator extends ConstraintValidator
{
  public function isValid($value, Constraint $constraint)
  {
    if (!is_null($value))
    {
      if ($constraint->multiple)
      {
        foreach ($value as $_value)
        {
          if (!in_array($_value, $constraint->choices, true))
          {
            $this->setMessage($constraint->message, array('value' => $_value));

            return false;
          }
        }

        $count = count($value);

        if (!is_null($constraint->min) && $count < $constraint->min)
        {
          $this->setMessage($constraint->minMessage, array('min' => $constraint->min));

          return false;
        }

        if (!is_null($constraint->max) && $count > $constraint->max)
        {
          $this->setMessage($constraint->maxMessage, array('max' => $constraint->max));

          return false;
        }
      }
      elseif (!in_array($value, $constraint->choices, true))
      {
        $this->setMessage($constraint->message, array('value' => $value));

        return false;
      }

      return true;
    }
  }
}
