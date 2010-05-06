<?php

namespace Symfony\Tests\Components\Form\Fixtures;

use Symfony\Components\Form\FieldInterface;
use Symfony\Components\I18N\Translatable;

interface TranslatableField extends FieldInterface, Translatable {}
