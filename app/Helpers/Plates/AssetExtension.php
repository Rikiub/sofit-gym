<?php

namespace App\Helpers\Plates;

use League\Plates\Extension\ExtensionInterface;
use League\Plates\Engine;

/**
 * Extension para la libreria Plates para poder agregar JS y CSS al \<head\>
 * desde cualquier vista o componente directamente.
 */
class AssetExtension implements ExtensionInterface
{
    protected array $css = [];
    protected array $js = [];

    public function __construct(private string $assets_dir) {}

    public function register(Engine $engine)
    {
        // Funciones para añadir assets
        $engine->registerFunction('pushCss', [$this, 'pushCss']);
        $engine->registerFunction('pushJs', [$this, 'pushJs']);

        // Funciones para renderizar assets
        $engine->registerFunction('renderCss', [$this, 'renderCss']);
        $engine->registerFunction('renderJs', [$this, 'renderJs']);
    }

    private function generatePath(string $path)
    {
        $file = $path;

        // Si no empieza con HTTPS, entonces preparar como archivo
        if (!str_starts_with($path, 'https://')) {
            $file = $this->assets_dir . "/" . $file;
        }

        return $file;
    }

    public function pushCss(string $path)
    {
        if (!isset($this->css[$path])) {
            $this->css[$this->generatePath($path)] = true;
        }
    }

    public function pushJs(string $path, bool $isModule = true)
    {
        if (!isset($this->js[$path])) {
            $this->js[$this->generatePath($path)] = $isModule;
        }
    }

    public function renderCss(): string
    {
        $tags = [];

        foreach (array_keys($this->css) as $path) {
            $tags[] = <<<HTML
                    <link rel="stylesheet" href="{$path}">
                HTML;
        }

        return implode("\n", $tags);
    }

    public function renderJs(): string
    {
        $tags = [];

        foreach ($this->js as $path => $isModule) {
            $type = $isModule ? 'type="module"' : 'defer';
            $tags[] = <<<HTML
                <script {$type} src="{$path}"></script>
                HTML;
        }

        return implode("\n", $tags);
    }
}
