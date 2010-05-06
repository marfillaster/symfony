<?php

namespace Symfony\Tests\Components\Form\Fixtures;

use Symfony\Components\Form\Renderer\RendererInterface;
use Symfony\Components\I18N\Translatable;

interface TranslatableRenderer extends RendererInterface, Translatable {}
