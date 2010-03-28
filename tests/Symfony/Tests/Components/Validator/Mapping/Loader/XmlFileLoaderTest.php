<?php

namespace Symfony\Tests\Components\Validator\Mapping\Loader;

require_once __DIR__.'/../../../../bootstrap.php';

use Symfony\Components\Validator\Constraints\NotNull;
use Symfony\Components\Validator\Constraints\Min;
use Symfony\Components\Validator\Constraints\Choice;
use Symfony\Components\Validator\Mapping\ClassMetadata;
use Symfony\Components\Validator\Mapping\Loader\XmlFileLoader;

class XmlFileLoaderTest extends \PHPUnit_Framework_TestCase
{
  public function testLoadClassMetadataReturnsTrueIfSuccessful()
  {
    $loader = new XmlFileLoader(__DIR__.'/definition.xml');
    $metadata = new ClassMetadata(__NAMESPACE__.'\XmlEntity');

    $this->assertTrue($loader->loadClassMetadata($metadata));
  }

  public function testLoadClassMetadataReturnsFalseIfNotSuccessful()
  {
    $loader = new XmlFileLoader(__DIR__.'/definition.xml');
    $metadata = new ClassMetadata(__NAMESPACE__.'\XmlFoobar');

    $this->assertFalse($loader->loadClassMetadata($metadata));
  }

  public function testLoadClassMetadata()
  {
    $loader = new XmlFileLoader(__DIR__.'/definition.xml');
    $metadata = new ClassMetadata(__NAMESPACE__.'\XmlEntity');

    $loader->loadClassMetadata($metadata);

    $expected = new ClassMetadata(__NAMESPACE__.'\XmlEntity');
    $expected->addConstraint(new NotNull());
    $expected->addConstraint(new Min(3));
    $expected->addConstraint(new Choice(array('A', 'B')));
    $expected->addConstraint(new Choice(array(
      'message' => 'Must be one of %choices%',
      'values'  => array('A', 'B'),
    )));
    $expected->addPropertyConstraint('property', new NotNull());
    $expected->addGetterConstraint('property', new NotNull());

    $this->assertEquals($expected, $metadata);
  }
}

class XmlEntity {}
class XmlFoobar {}