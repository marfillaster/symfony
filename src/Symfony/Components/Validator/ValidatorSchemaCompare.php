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
 * ValidatorSchemaCompare compares several values from an array.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: ValidatorSchemaCompare.php 51 2009-12-04 11:17:45Z robert $
 */
class ValidatorSchemaCompare extends ValidatorSchema
{
  const EQUAL              = '==';
  const NOT_EQUAL          = '!=';
  const IDENTICAL          = '===';
  const NOT_IDENTICAL      = '!==';
  const LESS_THAN          = '<';
  const LESS_THAN_EQUAL    = '<=';
  const GREATER_THAN       = '>';
  const GREATER_THAN_EQUAL = '>=';

  /**
   * Constructor.
   *
   * Available options:
   *
   *  * left_field:         The left field name
   *  * operator:           The comparison operator
   *                          * self::EQUAL
   *                          * self::NOT_EQUAL
   *                          * self::IDENTICAL
   *                          * self::NOT_IDENTICAL
   *                          * self::LESS_THAN
   *                          * self::LESS_THAN_EQUAL
   *                          * self::GREATER_THAN
   *                          * self::GREATER_THAN_EQUAL
   *  * right_field:        The right field name
   *  * throw_global_error: Whether to throw a global error (false by default) or an error tied to the left field
   *
   * @param string $leftField   The left field name
   * @param string $operator    The operator to apply
   * @param string $rightField  The right field name
   * @param array  $options     An array of options
   * @param array  $messages    An array of error messages
   *
   * @see BaseValidator
   */
  public function __construct($leftField, $operator, $rightField, $options = array(), $messages = array())
  {
    $this->addOption('left_field', $leftField);
    $this->addOption('operator', $operator);
    $this->addOption('right_field', $rightField);

    $this->addOption('throw_global_error', false);

    parent::__construct(null, $options, $messages);
  }

  /**
   * @see BaseValidator
   */
  protected function doClean($values)
  {
    if (null === $values)
    {
      $values = array();
    }

    if (!is_array($values))
    {
      throw new \InvalidArgumentException('You must pass an array parameter to the clean() method');
    }

    $leftValue  = isset($values[$this->getOption('left_field')]) ? $values[$this->getOption('left_field')] : null;
    $rightValue = isset($values[$this->getOption('right_field')]) ? $values[$this->getOption('right_field')] : null;

    switch ($this->getOption('operator'))
    {
      case self::GREATER_THAN:
        $valid = $leftValue > $rightValue;
        break;
      case self::GREATER_THAN_EQUAL:
        $valid = $leftValue >= $rightValue;
        break;
      case self::LESS_THAN:
        $valid = $leftValue < $rightValue;
        break;
      case self::LESS_THAN_EQUAL:
        $valid = $leftValue <= $rightValue;
        break;
      case self::NOT_EQUAL:
        $valid = $leftValue != $rightValue;
        break;
      case self::EQUAL:
        $valid = $leftValue == $rightValue;
        break;
      case self::NOT_IDENTICAL:
        $valid = $leftValue !== $rightValue;
        break;
      case self::IDENTICAL:
        $valid = $leftValue === $rightValue;
        break;
      default:
        throw new \InvalidArgumentException(sprintf('The operator "%s" does not exist.', $this->getOption('operator')));
    }

    if (!$valid)
    {
      $error = new ValidatorError($this, 'invalid', array(
        'left_field'  => $leftValue,
        'right_field' => $rightValue,
        'operator'    => $this->getOption('operator'),
      ));
      if ($this->getOption('throw_global_error'))
      {
        throw $error;
      }

      throw new ValidatorErrorSchema($this, array($this->getOption('left_field') => $error));
    }

    return $values;
  }

  /**
   * @see BaseValidator
   */
  public function asString($indent = 0)
  {
    $options = $this->getOptionsWithoutDefaults();
    $messages = $this->getMessagesWithoutDefaults();
    unset($options['left_field'], $options['operator'], $options['right_field']);

    $arguments = '';
    if ($options || $messages)
    {
      $arguments = sprintf('(%s%s)',
        $options ? \Symfony\Components\YAML\Inline::dump($options) : ($messages ? '{}' : ''),
        $messages ? ', '.\Symfony\Components\YAML\Inline::dump($messages) : ''
      );
    }

    return sprintf('%s%s %s%s %s',
      str_repeat(' ', $indent),
      $this->getOption('left_field'),
      $this->getOption('operator'),
      $arguments,
      $this->getOption('right_field')
    );
  }
}
