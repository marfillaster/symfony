<?php

namespace Symfony\Components\Validator\Engine;

use Symfony\Components\Validator\MetaDataInterface;
use Symfony\Components\Validator\ConstraintValidatorFactoryInterface;

class ExecutionContext
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
    $this->groups = $groups;
    $this->metaData = $metaData;
    $this->validatorFactory = $validatorFactory;
    $this->cachePrefixOffset = strlen(__NAMESPACE__ . '\\Validate');
  }

  public function execute(CommandInterface $command)
  {
    $key = $command->getCacheKey();
    $cachePrefix = substr(get_class($command), $this->cachePrefixOffset) . ':';

    if (is_null($key) || !isset($this->executed[$cachePrefix . $key]))
    {
      if (!is_null($key))
      {
        $this->executed[$cachePrefix . $key] = true;
      }

      $command->execute($this->violations, $this);
    }
//    else if (!is_null($key))
//    {
//      var_dump('cache hit!');
//    }

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