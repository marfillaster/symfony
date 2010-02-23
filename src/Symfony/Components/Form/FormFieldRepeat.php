<?php

namespace Symfony\Components\Form;

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @package    symfony
 * @subpackage form
 * @author     Bernhard Schussek <bernhard.schussek@symfony-project.com>
 * @version    SVN: $Id: FormFieldGroup.php 79 2009-12-08 12:53:15Z bernhard $
 */
class FormFieldRepeat extends FormFieldGroup
{
  private
    $classOrCallable = null;

  public function __construct($name, $classOrCallable, array $options = array())
  {
    if (is_string($classOrCallable))
    {
      if (!class_exists($classOrCallable))
      {
        throw new \LogicException(sprintf('Class %s does not exist', $classOrCallable));
      }

      $class = new \ReflectionClass($classOrCallable);
      if (!$class->implementsInterface('Symfony\Components\Form\FormFieldInterface'))
      {
        throw new \LogicException(sprintf('%s is expected to implement FormFieldInterface', $classOrCallable));
      }
    }
    else if (!is_callable($classOrCallable))
    {
      throw new \InvalidArgumentException('The second argument must be a class name or a callable');
    }

    $options = array_merge(array(
      'modifiable' => false,
    ), $options);

    $this->classOrCallable = $classOrCallable;

    if ($options['modifiable'])
    {
      $field = $this->createField('$$key$$');
      // TESTME
      $field->setRequired(false);
      $this->add($field);
    }

    parent::__construct($name, $options);
  }

  private function createField($name)
  {
    if (is_callable($this->classOrCallable))
    {
      $field = call_user_func($this->classOrCallable, $name);
    }
    else
    {
      $field = new $this->classOrCallable($name);
    }

    return $field;
  }

  public function setDefault($data)
  {
    if (!is_array($data))
    {
      throw new \InvalidArgumentException('The default data must be an array');
    }

    foreach ($data as $name => $value)
    {
      $this->add($this->createField($name));
    }

    parent::setDefault($data);
  }

  public function bind($taintedData)
  {
    if (is_null($taintedData))
    {
      $taintedData = array();
    }
    
    foreach ($this as $name => $field)
    {
      if (!isset($taintedData[$name]) && $this->getOption('modifiable') && $name != '$$key$$')
      {
        $this->remove($name);
      }
    }

    foreach ($taintedData as $name => $value)
    {
      if (!isset($this[$name]) && $this->getOption('modifiable'))
      {
        $this->add($this->createField($name));
      }
    }
    
    return parent::bind($taintedData);
  }
}