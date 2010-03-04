<?php

namespace Symfony\Components\Validator\Tests\MetaData;

require_once __DIR__.'/../TestInit.php';


use Symfony\Components\Validator\MetaData\ClassMetaData;
use Symfony\Components\Validator\MetaData\PropertyMetaData;
use Symfony\Components\Validator\Specification\ClassSpecification;
use Symfony\Components\Validator\Specification\PropertySpecification;
use Symfony\Components\Validator\Engine\Constraint;


class ClassMetaDataTest_InterfaceConstraint extends Constraint {}
class ClassMetaDataTest_ChildConstraint extends Constraint {}


class ClassMetaDataTest extends \PHPUnit_Framework_TestCase
{
  protected function assertPropertiesEqual(array $properties, ClassMetaData $class)
  {
    $this->assertEquals(array_keys($properties), $class->getConstrainedProperties());

    foreach ($properties as $property => $metaData)
    {
      $this->assertEquals($metaData, $class->getPropertyMetaData($property));
    }
  }

  public function testPropertiesAreTakenFromSpecification()
  {
    $property = new PropertySpecification('Class', 'property');
    $spec = new ClassSpecification('Class', array($property));
    $class = new ClassMetaData('Class', $spec);

    $expected = array(
      'property' => new PropertyMetaData('Class', 'property', $property),
    );

    $this->assertPropertiesEqual($expected, $class);
  }

  public function testParentPropertiesAreMerged()
  {
    $parentProperty = new PropertySpecification('Class', 'parentProperty');
    $spec = new ClassSpecification('Class', array($parentProperty));
    $parent = new ClassMetaData('Class', $spec);

    $childProperty = new PropertySpecification('SubClass', 'childProperty');
    $spec = new ClassSpecification('SubClass', array($childProperty));
    $class = new ClassMetaData('SubClass', $spec, $parent);

    $expected = array(
      'childProperty' => new PropertyMetaData('SubClass', 'childProperty', $childProperty),
      'parentProperty' => new PropertyMetaData('Class', 'parentProperty', $parentProperty),
    );

    $this->assertPropertiesEqual($expected, $class);
  }

  public function testExistingPropertiesRemainUnchanged()
  {
    $parentProperty = new PropertySpecification('Class', 'existingProperty');
    $spec = new ClassSpecification('Class', array($parentProperty));
    $parent = new ClassMetaData('Class', $spec);

    $childProperty = new PropertySpecification('SubClass', 'existingProperty');
    $spec = new ClassSpecification('SubClass', array($childProperty));
    $class = new ClassMetaData('SubClass', $spec, $parent);

    $expected = array(
      'existingProperty' => new PropertyMetaData('SubClass', 'existingProperty', $childProperty),
    );

    $this->assertPropertiesEqual($expected, $class);
  }

  public function testInterfacePropertiesAreMerged()
  {
    $interfaceProperty = new PropertySpecification('Class', 'interfaceProperty');
    $spec = new ClassSpecification('Class', array($interfaceProperty));
    $interface = new ClassMetaData('Class', $spec);

    $childProperty = new PropertySpecification('SubClass', 'childProperty');
    $spec = new ClassSpecification('SubClass', array($childProperty));
    $class = new ClassMetaData('SubClass', $spec, null, array($interface));

    $expected = array(
      'childProperty' => new PropertyMetaData('SubClass', 'childProperty', $childProperty),
      'interfaceProperty' => new PropertyMetaData('Class', 'interfaceProperty', $interfaceProperty),
    );

    $this->assertPropertiesEqual($expected, $class);
  }

  public function testInterfaceConstraintsAreMerged()
  {
    $interfaceConstraint = new ClassMetaDataTest_InterfaceConstraint();
    $spec = new ClassSpecification('Interface', array(), array($interfaceConstraint));
    $interface = new ClassMetaData('Interface', $spec);

    $childConstraint = new ClassMetaDataTest_ChildConstraint();
    $spec = new ClassSpecification('SubClass', array(), array($childConstraint));
    $element = new ClassMetaData('SubClass', $spec, null, array($interface));

    $expected = array(
      get_class($childConstraint) => $childConstraint,
      get_class($interfaceConstraint) => $interfaceConstraint,
    );

    $this->assertEquals($expected, $element->getConstraints());
  }
}