<?php

namespace Symfony\Components\Validator;

interface SpecificationInterface
{
  public function getClassSpecification($class);

  public function getGroupSpecification($interface);
}