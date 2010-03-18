<?php

namespace Symfony\Components\Validator\Mapping\Loader;

use Symfony\Components\Validator\Mapping\ClassMetadata;
use Symfony\Components\Validator\Mapping\GroupMetadata;

abstract class FileLoader implements LoaderInterface
{
  protected $paths;
  protected $extension;

  public function __construct(array $paths)
  {
    $this->paths = $paths;
  }

  protected function findMappingFile($class)
  {
    $fileName = str_replace('\\', '.', $class) . $this->extension;

    foreach ($this->paths as $path)
    {
      if (file_exists($path . DIRECTORY_SEPARATOR . $fileName))
      {
        return $path . DIRECTORY_SEPARATOR . $fileName;
      }
    }

    return false;
  }

  public function loadClassMetadata(ClassMetadata $metadata)
  {
    if ($fileName = $this->findMappingFile($metadata->getName()))
    {
      $this->parseClassMetadata($fileName, $metadata);
    }
  }

  abstract protected function parseClassMetadata($file, ClassMetadata $metadata);
}