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
	exit('<!doctype html><html><head><meta charset="utf-8"><title>BEL-CMS : Error 403 Forbidden</title><style>h1{margin: 20px auto;text-align:center;color: red;}p{text-align:center;font-weight:bold;</style></head><body><h1>HTTP Error 403 : Forbidden</h1><p>You don\'t permission to access / on this server.</p></body></html>');
endif;

use RuntimeException;

final class View
{
    private string $viewsPath;

    public function __construct(?string $viewsPath = null)
    {
        $this->viewsPath = $viewsPath ?? dirname(__DIR__, 2) . '/modules';
    }

    public function render(string $module, string $view, array $data = []): string
    {
        $file = $this->viewsPath
            . DIRECTORY_SEPARATOR . $module
            . DIRECTORY_SEPARATOR . 'Views'
            . DIRECTORY_SEPARATOR . $view
            . '.php';

        if (!is_file($file)) {
            throw new RuntimeException("Vue introuvable : {$file}");
        }

        extract($data, EXTR_SKIP);
        ob_start();
        require $file;
        return ob_get_clean();
    }
}
