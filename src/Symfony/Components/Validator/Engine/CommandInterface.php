<?php

namespace Symfony\Components\Validator\Engine;

interface CommandInterface
{
  function execute(ConstraintViolationList $violations, LocalExecutionContext $context);

  function getCacheKey(LocalExecutionContext $context);
}