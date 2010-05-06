<?php

namespace Symfony\Tests\Components\Form\Fixtures;

use Symfony\Components\Form\ValueTransformer\ValueTransformerInterface;
use Symfony\Components\I18N\Localizable;

interface LocalizableValueTransformer extends ValueTransformerInterface, Localizable {}
