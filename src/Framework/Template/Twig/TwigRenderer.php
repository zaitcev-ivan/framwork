<?php

namespace Framework\Template\Twig;

use Framework\Template\TemplateRenderer;
use Twig\Environment;

/**
 * Class TwigRenderer
 * @package Framework\Template\Twig
 */
class TwigRenderer implements TemplateRenderer
{
    private $twig;
    private $extension;

    public function __construct(Environment $twig, $extension)
    {
        $this->twig = $twig;
        $this->extension = $extension;
    }

    /**
     * @param $name
     * @param array $params
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function render($name, array $params = []): string
    {
        return $this->twig->render($name . $this->extension, $params);
    }
}