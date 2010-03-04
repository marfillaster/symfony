<?php

namespace Symfony\Components\Validator\Engine\Execution;

use Symfony\Components\Validator\Engine\ConstraintViolationList;

interface CommandInterface
{
  function execute(ConstraintViolationList $violations, ExecutionContext $context);

  function getCacheKey();
}