<?php

/*
 * This file bootstraps the test environment.
 */
namespace Symfony\Components\Form\Tests;

use Symfony\Components\Form\FormFieldInterface;
use Symfony\Components\Form\Renderer\RendererInterface;
use Symfony\Components\Validator\ValidatorInterface;
use Symfony\Components\ValueTransformer\ValueTransformerInterface;

use Symfony\Components\I18N\Localizable;
use Symfony\Components\I18N\Translatable;
use Symfony\Components\I18N\TranslatorInterface;

error_reporting(E_ALL | E_STRICT);

require_once 'PHPUnit/Framework.php';
require_once __DIR__ . '/ClassLoader.php';

$classLoader = new ClassLoader(
  'Symfony\Components',
  __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..'
);
$classLoader->register();

interface LocalizableRenderer extends RendererInterface, Localizable {}
interface TranslatableRenderer extends RendererInterface, Translatable {}
interface LocalizableValidator extends ValidatorInterface, Localizable {}
interface TranslatableValidator extends ValidatorInterface, Translatable {}
interface LocalizableValueTransformer extends ValueTransformerInterface, Localizable {}
interface TranslatableValueTransformer extends ValueTransformerInterface, Translatable {}
interface LocalizableField extends FormFieldInterface, Localizable {}
interface TranslatableField extends FormFieldInterface, Translatable {}