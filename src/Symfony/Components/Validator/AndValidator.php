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
 * AndValidator validates an input value if all validators passes.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Florian Eckerstorfer <florian@eckerstorfer.org>
 * @version    SVN: $Id: AndValidator.php 166 2010-01-24 21:22:55Z flo $
 */
class AndValidator extends BaseValidator
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
      throw new \InvalidArgumentException('AndValidator constructor takes a ValidatorInterface object, or a ValidatorInterface array.');
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
   * Validates whether all of the validators passed to the constructor validate. The validators may be given as array or as
   * dynamic argument list.
   *
   * @param mixed $value 
   * @return void
   * @throws ValidatorErrorSchema when one of the validators fails.
   */
  protected function doValidate($value)
  {
    $messages = array();
    foreach ($this->getValidators() as $validator)
    {
      try
      {
        $validator->validate($value);
      }
      catch (ValidatorError $e)
      {
        $messages[] = $e;
      }
    }
    if (count($messages) > 0)
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
  
  /**
   * @see BaseValidator
   */
  public function asString($indent = 0)
  {
    $validators = '';
    for ($i = 0, $max = count($this->validators); $i < $max; $i++)
    {
      $validators .= "\n".$this->validators[$i]->asString($indent + 2)."\n";

      if ($i < $max - 1)
      {
        $validators .= str_repeat(' ', $indent + 2).'and';
      }

      if ($i == $max - 2)
      {
        $options = $this->getOptionsWithoutDefaults();
        $messages = $this->getMessagesWithoutDefaults();

        if ($options || $messages)
        {
          $validators .= sprintf('(%s%s)',
            $options ? \Symfony\Components\YAML\Inline::dump($options) : ($messages ? '{}' : ''),
            $messages ? ', '.\Symfony\Components\YAML\Inline::dump($messages) : ''
          );
        }
      }
    }

    return sprintf("%s(%s%s)", str_repeat(' ', $indent), $validators, str_repeat(' ', $indent));
  }
}
