<?php

namespace Symfony\Components\Validator\Constraints;

class AssertType extends \Symfony\Components\Validator\Constraint
{
  public $message = 'Symfony.Validator.AssertType.message';
  public $type;

  /**
   * {@inheritDoc}
   */
  public function defaultAttribute()
  {
    return 'type';
  }

  /**
   * {@inheritDoc}
   */
  public function requiredAttributes()
  {
    return array('type');
  }
}
