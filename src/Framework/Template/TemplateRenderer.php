<?php

namespace Framework\Template;

/**
 * Interface TemplateRenderer
 * @package Framework\Template
 */
interface TemplateRenderer
{
    public function render($view, array $params = []): string;
}