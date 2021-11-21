<?php

namespace Befew;

use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Twig\TwigFunction;

class Template {
    private Environment $twig;
    private Path $templatePath;
    private Path $assetsPath;
    private array $styles = [];
    private array $scripts = [];
    private array $footScripts = [];
    private array $data = [];

    public function __construct(string $templatePath, ?string $assetsPath = null) {
        $this->styles = [];
        $this->scripts = [];
        $this->templatePath = new Path($templatePath);
        $this->assetsPath = new Path($assetsPath ?? $templatePath);

        $loader = new FilesystemLoader(BEFEW_BASE_URL);
        $this->twig = new Environment($loader, [
            'cache' => CACHE_TWIG_FOLDER,
            'debug' => DEBUG
        ]);

        $assetFunction = new TwigFunction('asset', function (string $path): string {
            return $this->assetsPath . DIRECTORY_SEPARATOR . $path;
        });

        $this->twig->addFunction($assetFunction);

        $this->data = [
            'errors' => [],
            'warnings' => [],
            'infos' => []
        ];
    }

    public function addMessage(string $level, string $string): Template {
        if(!in_array($level, ['error', 'warning', 'info'])) {
            $level = 'info';
        }

        $this->data[$level . 's'][] = $string;

        return $this;
    }

    public function addCSS(string $path): Template {
        if (strpos($path, 'http') === false) {
            $path = $this->assetsPath->getPathWithWebSeparators() . STYLES_FOLDER . '/' . $path;
        }

        array_push($this->styles, $path);

        return $this;
    }

    public function addJS(string $path, bool $head = true): Template {
        if (strpos($path, 'http') === false) {
            $path = $this->assetsPath->getPathWithWebSeparators() . SCRIPTS_FOLDER . '/' . $path;
        }

        if ($head) {
            $this->scripts[] = $path;
        } else {
            $this->footScripts[] = $path;
        }

        return $this;
    }

    /**
     * @param string $file
     * @param array $vars
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function render(string $file, array $vars = []): void {
        echo $this->twig->render(
            substr($this->templatePath, strlen(BEFEW_BASE_URL)) . TEMPLATES_FOLDER . DIRECTORY_SEPARATOR . $file,
            array_merge(
                $vars,
                $this->data,
                [
                    'styles' => $this->styles,
                    'scripts' => $this->scripts,
                    'footScripts' => $this->footScripts,
                    'baseUrl' => Request::getInstance()->getBaseURL(),
                ]
            )
        );
    }
}