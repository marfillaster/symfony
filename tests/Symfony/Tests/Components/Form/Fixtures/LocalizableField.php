<?php

namespace Symfony\Tests\Components\Form\Fixtures;

use Symfony\Components\Form\FieldInterface;
use Symfony\Components\I18N\Localizable;

interface LocalizableField extends FieldInterface, Localizable {}
