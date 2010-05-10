<?php

namespace Symfony\Components\Validator\Constraints;

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Choice extends \Symfony\Components\Validator\Constraint
{
  public $choices = array();
  public $callback;
  public $message = '%value% is not a valid choice';
  public $multiple = false;
  public $min;
  public $minMessage = 'Please select at least %min% choices';
  public $max;
  public $maxMessage = 'Please select no more than %max% choices';

  public function defaultAttribute()
  {
    return 'choices';
  }
}
