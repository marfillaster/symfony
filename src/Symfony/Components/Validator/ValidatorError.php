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
 * ValidatorError represents a validation error.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: ValidatorError.php 154 2010-01-22 21:52:03Z robert $
 */
class ValidatorError extends \RuntimeException
{
  protected
    $field = null;
  
  public function __construct($message, $field = null)
  {
    parent::__construct($message);
    $this->field = $field;
  }
}
