<?php

namespace Symfony\Components\Validator\Engine\Execution;

use Symfony\Components\Validator\MetaDataInterface;
use Symfony\Components\Validator\ConstraintValidatorFactoryInterface;
use Symfony\Components\Validator\Engine\ConstraintViolationList;

class ExecutionContext
{
  protected $root;
  protected $groups;
  protected $metaData;
  protected $validatorFactory;
  protected $executed = array();
  protected $context;
  protected $violations;

  public function __construct($root, array $groups, MetaDataInterface $metaData, ConstraintValidatorFactoryInterface $validatorFactory)
  {
    $this->violations = new ConstraintViolationList();
    $this->root = $root;
    $this->groups = $groups;
    $this->metaData = $metaData;
    $this->validatorFactory = $validatorFactory;
  }

  public function execute(CommandInterface $command)
  {
    $key = get_class($command).$command->getCacheKey();

    if (!isset($this->executed[$key]))
    {
      $this->executed[$key] = true;

      $command->execute($this->violations, $this);
    }

    return $this->violations;
  }

  public function getRoot()
  {
    return $this->root;
  }

  public function getGroups()
  {
    return $this->groups;
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