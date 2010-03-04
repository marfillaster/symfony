<?php

namespace Symfony\Components\Validator\Engine;

interface CommandInterface
{
  function execute(ConstraintViolationList $violations, ExecutionContext $context);

  function getCacheKey();
}