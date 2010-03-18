<?php

namespace Symfony\Components\Validator\Mapping\Loader;

use Symfony\Components\Validator\Mapping\ClassMetadata;
use Symfony\Components\Validator\Mapping\GroupMetadata;

interface LoaderInterface
{
  function loadClassMetadata(ClassMetadata $metadata);
}