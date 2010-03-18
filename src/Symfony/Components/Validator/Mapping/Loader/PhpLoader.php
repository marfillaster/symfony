<?php

namespace Symfony\Components\Validator\Mapping\Loader;

use Symfony\Components\Validator\Mapping\ClassMetadata;
use Symfony\Components\Validator\Mapping\GroupMetadata;

class PhpLoader extends FileLoader
{
  protected $extension = '.val.php';

  protected function parseClassMetadata($file, ClassMetadata $metadata)
  {
    require $file;
  }
}