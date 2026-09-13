<?php
/**
 * Bel-CMS [Content management system]
 * @version 5.0.0 [PHP8.5]
 * @link https://bel-cms.dev
 * @link https://determe.be
 * @license Apache-2.0 license
 * @copyright 2015-2026 Bel-CMS
 * @author as Stive - stive@determe.be
*/

declare(strict_types=1);

namespace BelCMS\Core;

if (!defined('CHECK_INDEX')):
    header($_SERVER['SERVER_PROTOCOL'] . ' 403 Direct access forbidden');
    exit('<!doctype html><html><head><meta charset="utf-8"><title>BEL-CMS : Error 403 Forbidden</title><style>h1{margin:20px auto;text-align:center;color:red;}p{text-align:center;font-weight:bold;}</style></head><body><h1>HTTP Error 403 : Forbidden</h1><p>You don\'t permission to access / on the server.</p></body></html>');
endif;

final class Assets
{
    private array $css = [];
    private array $js = [];

    /**
     * Ajoute un fichier CSS.
     */
    public function css(string $file): void
    {
        if (!in_array($file, $this->css, true)) {
            $this->css[] = $file;
        }
    }

    /**
     * Ajoute un fichier JavaScript.
     */
    public function js(string $file): void
    {
        if (!in_array($file, $this->js, true)) {
            $this->js[] = $file;
        }
    }

    /**
     * Ajoute le CSS principal d'un module.
     * Exemple : /modules/Profils/css/profils.css
     */
    public function moduleCss(string $module): void
    {
        $file = '/modules/'
            . $module
            . '/css/'
            . strtolower($module)
            . '.css';

        $this->css($file);
    }

    /**
     * Ajoute le JavaScript principal d'un module.
     * Exemple : /modules/Profils/js/profils.js
     */
    public function moduleJs(string $module): void
    {
        $file = '/modules/'
            . $module
            . '/js/'
            . strtolower($module)
            . '.js';

        $this->js($file);
    }

    /**
     * Ajoute un fichier CSS spécifique à un module.
     * Exemple : /modules/Profils/css/edit.css
     */
    public function moduleCssFile(string $module, string $file): void
    {
        $path = '/modules/'
            . trim($module, '/')
            . '/css/'
            . ltrim($file, '/');

        $this->css($path);
    }

    /**
     * Ajoute un fichier JavaScript spécifique à un module.
     * Exemple : /modules/Profils/js/edit.js
     */
    public function moduleJsFile(string $module, string $file): void
    {
        $path = '/modules/'
            . trim($module, '/')
            . '/js/'
            . ltrim($file, '/');

        $this->js($path);
    }

    /**
     * Ajoute un plugin CSS.
     */
    public function pluginCss(string $file): void
    {
        $this->css(
            '/assets/plugins/' . ltrim($file, '/')
        );
    }

    /**
     * Ajoute un plugin JavaScript.
     */
    public function pluginJs(string $file): void
    {
        $this->js(
            '/assets/plugins/' . ltrim($file, '/')
        );
    }

    /**
     * Retourne les fichiers CSS.
     */
    public function getCss(): array
    {
        return $this->css;
    }

    /**
     * Retourne les fichiers JavaScript.
     */
    public function getJs(): array
    {
        return $this->js;
    }

    /**
     * Génère les balises CSS.
     */
    public function renderCss(): string
    {
        $html = '';

        foreach ($this->css as $file) {
            $html .= sprintf(
                '<link rel="stylesheet" href="%s">' . PHP_EOL,
                htmlspecialchars($file, ENT_QUOTES, 'UTF-8')
            );
        }

        return $html;
    }

    /**
     * Génère les balises JavaScript.
     */
    public function renderJs(): string
    {
        $html = '';

        foreach ($this->js as $file) {
            $html .= sprintf(
                '<script src="%s"></script>' . PHP_EOL,
                htmlspecialchars($file, ENT_QUOTES, 'UTF-8')
            );
        }

        return $html;
    }
}
