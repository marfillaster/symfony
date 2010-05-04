<?php

namespace Symfony\Tests\Components\Form\Fixtures;

use Symfony\Components\Form\ValueTransformer\ValueTransformerInterface;
use Symfony\Components\I18N\Translatable;

interface TranslatableValueTransformer extends ValueTransformerInterface, Translatable {}