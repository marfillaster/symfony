<?php

namespace Symfony\Tests\Components\Validator\Mapping\Loader;

require_once __DIR__.'/../../../../bootstrap.php';
require_once __DIR__.'/../../Fixtures/Entity.php';

use Symfony\Components\Validator\Constraints\NotNull;
use Symfony\Components\Validator\Constraints\Min;
use Symfony\Components\Validator\Constraints\Choice;
use Symfony\Components\Validator\Mapping\ClassMetadata;
use Symfony\Components\Validator\Mapping\Loader\YamlFileLoader;

class YamlFileLoaderTest extends \PHPUnit_Framework_TestCase
{
  public function testLoadClassMetadataReturnsTrueIfSuccessful()
  {
    $loader = new YamlFileLoader(__DIR__.'/constraint-mapping.yml');
    $metadata = new ClassMetadata('Symfony\Tests\Components\Validator\Fixtures\Entity');

    $this->assertTrue($loader->loadClassMetadata($metadata));
  }

  public function testLoadClassMetadataReturnsFalseIfNotSuccessful()
  {
    $loader = new YamlFileLoader(__DIR__.'/constraint-mapping.yml');
    $metadata = new ClassMetadata('\stdClass');

    $this->assertFalse($loader->loadClassMetadata($metadata));
  }

  public function testLoadClassMetadata()
  {
    $loader = new YamlFileLoader(__DIR__.'/constraint-mapping.yml');
    $metadata = new ClassMetadata('Symfony\Tests\Components\Validator\Fixtures\Entity');

    $loader->loadClassMetadata($metadata);

    $expected = new ClassMetadata('Symfony\Tests\Components\Validator\Fixtures\Entity');
    $expected->addConstraint(new NotNull());
    $expected->addConstraint(new Min(3));
    $expected->addConstraint(new Choice(array('A', 'B')));
    $expected->addPropertyConstraint('firstName', new Choice(array(
      'message' => 'Must be one of %choices%',
      'choices' => array('A', 'B'),
    )));
    $expected->addGetterConstraint('lastName', new NotNull());

    $this->assertEquals($expected, $metadata);
  }
}
