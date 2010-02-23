<?php

namespace Symfony\Components\Validator;

use Symfony\Components\Form\Callable;

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * RegexValidator validates a value with a regular expression.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: RegexValidator.php 183 2010-01-25 13:07:06Z bernhard $
 */
class RegexValidator extends StringValidator
{
  /**
   * Configures the current validator.
   *
   * Available options:
   *
   *  * pattern:    A regex pattern compatible with PCRE or {@link Callable} that returns one (required)
   *  * must_match: Whether the regex must match or not (true by default)
   *
   * @param array $options   An array of options
   * @param array $messages  An array of error messages
   *
   * @see StringValidator
   */
  protected function configure($options = array(), $messages = array())
  {
    parent::configure($options, $messages);

    $this->addRequiredOption('pattern');
    $this->addOption('must_match', true);
  }

  /**
   * Validates the value.
   *
   * @param  mixed $value   The value that should be validated
   * @throws InvalidArgumentException when the argument is not of the
   *                                  expected type
   * @throws ValidatorError when the validation fails
   *
   * @see StringValidator
   */
  protected function doValidate($value)
  {
    parent::doValidate($value);

    $string = (string) $value;

    $pattern = $this->getPattern();
    
    if ($pattern instanceof Callable)
    {
      $pattern = $pattern->call();
    }

    if (
      ($this->getOption('must_match') && !preg_match($pattern, $string))
      ||
      (!$this->getOption('must_match') && preg_match($pattern, $string))
    )
    {
      throw new ValidatorError($this->getMessage('invalid', array('value' => $value)));
    }
  }

  /**
   * Returns the current validator's regular expression.
   *
   * @return string
   */
  public function getPattern()
  {
    $pattern = $this->getOption('pattern');

    return $pattern instanceof Callable ? $pattern->call() : $pattern;
  }
}
