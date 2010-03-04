<?php

namespace Symfony\Components\Validator\Engine;

class LocalExecutionContext
{
  protected $context;
  protected $groups;

  public function __construct(GlobalExecutionContext $context, array $groups)
  {
    $this->context = $context;
    $this->groups = $groups;
  }

  public function execute(CommandInterface $command)
  {
    return $this->context->executeInContext($command, $this);
  }

  public function getRoot()
  {
    return $this->context->getRoot();
  }

  public function getMetaData()
  {
    return $this->context->getMetaData();
  }

  public function getValidatorFactory()
  {
    return $this->context->getValidatorFactory();
  }

  public function getGroups()
  {
    return $this->groups;
  }
}