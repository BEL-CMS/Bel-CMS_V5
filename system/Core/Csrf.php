<?php
/**
 * Bel-CMS [Content management system]
 * @version 5.0.0 [PHP8.5]
 * @link https://bel-cms.dev
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

final class Csrf
{
    private const SESSION_KEY = 'BELCMS_CSRF_TOKEN';
    private const TOKEN_LENGTH = 32;

    private Session $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function token(): string
    {
        $token = $this->session->get(self::SESSION_KEY);

        if (!is_string($token) || $token === '') {
            $token = bin2hex(random_bytes(self::TOKEN_LENGTH));
            $this->session->set(self::SESSION_KEY, $token);
        }

        return $token;
    }

    public function field(): string
    {
        return sprintf(
            '<input type="hidden" name="csrf_token" value="%s">',
            htmlspecialchars($this->token(), ENT_QUOTES, 'UTF-8')
        );
    }

    public function verify(?string $token): bool
    {
        if (!is_string($token) || $token === '') {
            return false;
        }

        $sessionToken = $this->session->get(self::SESSION_KEY);

        if (!is_string($sessionToken) || $sessionToken === '') {
            return false;
        }

        return hash_equals($sessionToken, $token);
    }

    public function regenerate(): string
    {
        $token = bin2hex(random_bytes(self::TOKEN_LENGTH));
        $this->session->set(self::SESSION_KEY, $token);
        return $token;
    }

    public function clear(): void
    {
        $this->session->remove(self::SESSION_KEY);
    }
}
