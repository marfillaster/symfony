<?php

namespace Symfony\Tests\Components\Form\Fixtures;

use Symfony\Components\Form\Renderer\RendererInterface;
use Symfony\Components\I18N\Localizable;

interface LocalizableRenderer extends RendererInterface, Localizable {}
