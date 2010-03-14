<?php

namespace Symfony\Components\Validator\Engine;

use Symfony\Components\Validator\Exception\ValidatorException;
use Symfony\Components\Validator\MetaDataInterface;
use Symfony\Components\Validator\ConstraintValidatorFactoryInterface;

class GlobalExecutionContext
{
  protected $root;
  protected $groups;
  protected $metaData;
  protected $validatorFactory;
  protected $executed = array();
  protected $context;
  protected $violations;
  protected $cachePrefixOffset;

  public function __construct($root, array $groups, MetaDataInterface $metaData, ConstraintValidatorFactoryInterface $validatorFactory)
  {
    $this->violations = new ConstraintViolationList();
    $this->root = $root;
    $this->metaData = $metaData;
    $this->validatorFactory = $validatorFactory;
    $this->cachePrefixOffset = strlen(__NAMESPACE__ . '\\Validate');
    $this->sequence = false;

    foreach ($groups as $key => $interface)
    {
      $this->groups[$key] = $this->metaData->getGroupMetaData($interface);
      $this->sequence = $this->sequence || $this->groups[$key]->isGroupSequence();
    }

    if ($this->sequence)
    {
      if (count($this->groups) > 1)
      {
        throw new ValidatorException('Group sequences cannot be validated together with other groups');
      }
      else
      {
        reset($this->groups);
        $this->sequence = new GroupSequence(current($this->groups)->getGroupSequence());
      }
    }
  }

  public function execute(CommandInterface $command)
  {
    if ($this->sequence)
    {
      foreach ($this->sequence as $group)
      {
        $this->executeInContext($command, new LocalExecutionContext($this, array($group)));

        if (count($this->violations) > 0)
        {
          break;
        }
      }
    }
    else
    {
      $this->executeInContext($command, new LocalExecutionContext($this, $this->groups));
    }

    return $this->violations;
  }

  public function getRoot()
  {
    return $this->root;
  }

  public function getMetaData()
  {
    return $this->metaData;
  }

  public function getValidatorFactory()
  {
    return $this->validatorFactory;
  }
}