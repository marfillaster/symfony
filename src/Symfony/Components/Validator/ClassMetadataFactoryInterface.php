<?php

namespace Symfony\Components\Validator;

interface ClassMetadataFactoryInterface
{
  function getClassMetadata($class);

  function getClassMetadatas(array $classes);
}