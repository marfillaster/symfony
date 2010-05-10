<?php

namespace Symfony\Components\Validator\Constraints;

class Min extends \Symfony\Components\Validator\Constraint
{
  public $message = 'Symfony.Validator.Min.message';
  public $limit;

  /**
   * {@inheritDoc}
   */
  public function defaultAttribute()
  {
    return 'limit';
  }

  /**
   * {@inheritDoc}
   */
  public function requiredAttributes()
  {
    return array('limit');
  }
}