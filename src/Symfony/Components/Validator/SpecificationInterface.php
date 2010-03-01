<?php

namespace Symfony\Components\Validator;

use Symfony\Components\Validator\Specification\ClassSpecification;

interface SpecificationInterface
{
  public function getClassSpecification($class);
}