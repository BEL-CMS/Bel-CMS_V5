<?php

declare(strict_types=1);

namespace BelCMS\Core;

use RuntimeException;

final class Language
{
    private string $language;
    private array $translations = [];

    public function __construct(string $language = 'fr')
    {
        $this->setLanguage($language);
    }
    /**
     * Définit la langue courante
     */
    public function setLanguage(string $language): void
    {
        $language = strtolower(trim($language));

        if ($language === '') {
            $language = 'fr';
        }

        $this->language = $language;
    }
    /**
     * Retourne la langue courante
     */
    public function getLanguage(): string
    {
        return $this->language;
    }
    /**
     * Charge un fichier de langue
     */
    public function load(string $file): void
    {
        if (!is_file($file)) {
            throw new RuntimeException(
                'Fichier de langue introuvable : ' . $file
            );
        }
        $translations = require $file;
        if (!is_array($translations)) {
            throw new RuntimeException(
                'Le fichier de langue doit retourner un tableau : '
                . $file
            );
        }
        /*
         * Les nouvelles traductions écrasent
         * une clé existante.
         */
        $this->translations = array_merge(
            $this->translations,
            $translations
        );
    }
    /**
     * Charge les langues globales
     */
    public function loadGlobal(): void
    {
        $file = dirname(__DIR__, 2)
            . '/langs/lang.'
            . $this->language
            . '.php';

        if (!is_file($file)) {
            return;
        }

        $this->load($file);
    }
    /**
     * Charge les langues d'un module
     */
    public function loadModule(string $module): void
    {
        $file = dirname(__DIR__, 2)
            . '/modules/'
            . $module
            . '/langs/lang.'
            . $this->language
            . '.php';

        if (!is_file($file)) {
            return;
        }

        $this->load($file);
    }
    /**
     * Retourne une traduction
     */
    public function get(string $key, ?string $default = null): string 
    {
        if (array_key_exists($key, $this->translations) && is_string($this->translations[$key])) {
            return $this->translations[$key];
        }

        return $default ?? $key;
    }
    /**
     * Vérifie si une traduction existe
     */
    public function has(string $key): bool
    {
        return array_key_exists(
            $key,
            $this->translations
        );
    }
    /**
     * Retourne toutes les traductions
     */
    public function all(): array
    {
        return $this->translations;
    }
    /**
     * Réinitialise les traductions
     */
    public function clear(): void
    {
        $this->translations = [];
    }
}