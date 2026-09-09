<?php

declare(strict_types=1);

namespace BelCMS\Core;

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
