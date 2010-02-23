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
 * Validates whether any of the validators passed to the constructor validates. The validators may be given as array
 * or as dynamic argument list.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Florian Eckerstorfer <florian@eckerstorfer.org>
 * @version    SVN: $Id: OrValidator.php 166 2010-01-24 21:22:55Z flo $
 */
class OrValidator extends BaseValidator
{
  protected
    $validators = array();

  /**
   * Constructor.
   *
   * The first argument can be:
   *
   *  * null
   *  * a BaseValidator instance
   *  * an array of BaseValidator instances
   *
   * @param mixed $validators Initial validators
   * @param array $options    An array of options
   * @param array $messages   An array of error messages
   *
   * @see BaseValidator
   */
  public function __construct($validators = null, $options = array(), $messages = array())
  {
    if ($validators instanceof ValidatorInterface)
    {
      $this->addValidator($validators);
    }
    else if (is_array($validators))
    {
      foreach ($validators as $validator)
      {
        $this->addValidator($validator);
      }
    }
    else if (null !== $validators)
    {
      throw new \InvalidArgumentException('OrValidator constructor takes a ValidatorInterface object, or a ValidatorInterface array.');
    }

    parent::__construct($options, $messages);
  }

  /**
   * Configures the current validator.
   *
   * Available options:
   *
   *  * halt_on_error: Whether to halt on the first error or not (false by default)
   *
   * @param array $options   An array of options
   * @param array $messages  An array of error messages
   *
   * @see BaseValidator
   */
  protected function configure($options = array(), $messages = array())
  {
    $this->addOption('halt_on_error', false);

    $this->setMessage('invalid', null);
  }
  
  public function validate($value)
  {
    $this->doValidate($value);
  }
  
  /**
   * Validates whether any of the validators passed to the constructor validates. The validators may be given as array
   * or as dynamic argument list.
   *
   * @param mixed $value 
   * @return void
   * @throws ValidatorErrorSchema when one of the validators fails.
   */
  protected function doValidate($value)
  {
    $messages = array();
    $valid = 0;
    foreach ($this->getValidators() as $validator)
    {
      try
      {
        $validator->validate($value);
        ++$valid;
      }
      catch (ValidatorError $e)
      {
        $messages[] = $e;
      }
    }
    if (0 == $valid)
    {
      if ($this->getMessage('invalid'))
      {
        throw new ValidatorError($this->getMessage('invalid'));
      }
      else
      {
        throw new ValidatorErrorSchema($messages);
      }
    }
  }

  /**
   * Adds a validator.
   *
   * @param ValidatorInterface $validator  A ValidatorInterface instance
   */
  public function addValidator(ValidatorInterface $validator)
  {
    $this->validators[] = $validator;
  }

  /**
   * Returns an array of the validators.
   *
   * @return array An array of BaseValidator instances
   */
  public function getValidators()
  {
    return $this->validators;
  }
}
