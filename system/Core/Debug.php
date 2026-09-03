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

final class Debug
{
    /**
     * Affiche une variable de debug
     *
     * @param mixed $data
     * @param bool $exitAfter Arrête le script après le debug
     * @param bool $collapse Ferme les tableaux/objets par défaut
     */
    public static function dump(
        mixed $data,
        bool $exitAfter = true,
        bool $collapse = false
    ): void {
        $isCli = PHP_SAPI === 'cli';

        if (!$isCli) {
            self::javascript();
        }

        self::display(
            $data,
            $isCli,
            0,
            $collapse
        );

        if ($exitAfter) {
            exit;
        }
    }

    /**
     * Affichage récursif
     */
    private static function display(
        mixed $data,
        bool $isCli,
        int $level,
        bool $collapse
    ): void {
        $type = self::getType($data);

        $length = match ($type) {
            'Array'  => count($data),
            'String' => strlen($data),
            default  => null,
        };

        if ($type === 'Object' || $type === 'Array') {

            self::displayContainer(
                $data,
                $type,
                $length,
                $isCli,
                $level,
                $collapse
            );

            return;
        }

        self::displayValue(
            $data,
            $type,
            $length,
            $isCli
        );
    }

    /**
     * Retourne le type lisible
     */
    private static function getType(
        mixed $data
    ): string {
        return match (true) {
            is_null($data)    => 'NULL',
            is_bool($data)    => 'Boolean',
            is_int($data)     => 'Integer',
            is_float($data)   => 'Float',
            is_string($data)  => 'String',
            is_array($data)   => 'Array',
            is_object($data)  => 'Object',
            is_resource($data) => 'Resource',
            is_callable($data) => 'Callable',
            default           => ucfirst(gettype($data)),
        };
    }

    /**
     * Affiche un tableau ou objet
     */
    private static function displayContainer(
        array|object $data,
        string $type,
        ?int $length,
        bool $isCli,
        int $level,
        bool $collapse
    ): void {
        $values = is_object($data)
            ? get_object_vars($data)
            : $data;

        /*
         * Mode terminal
         */
        if ($isCli) {

            self::cliIndent($level);

            echo $type;

            if ($length !== null) {
                echo '(' . $length . ')';
            }

            echo PHP_EOL;

            foreach ($values as $key => $value) {

                self::cliIndent($level);

                echo '[' . $key . '] => ';

                self::display(
                    $value,
                    true,
                    $level + 1,
                    $collapse
                );
            }

            return;
        }

        /*
         * Génère un identifiant unique
         */
        $id = substr(
            md5(
                uniqid(
                    (string) mt_rand(),
                    true
                )
            ),
            0,
            8
        );

        $display = $collapse
            ? 'none'
            : 'inline';

        $arrow = $collapse
            ? '&#10549;'
            : '';

        echo '<div class="bel-debug-container">';

        echo '<a'
            . ' href="javascript:void(0)"'
            . ' onclick="BelCMSDebug.toggle(\''
            . $id
            . '\')"'
            . ' class="bel-debug-toggle">'
            . '<span>'
            . htmlspecialchars($type)
            . ($length !== null
                ? '(' . $length . ')'
                : '')
            . '</span>'
            . '<span'
            . ' id="plus'
            . $id
            . '"'
            . '>'
            . $arrow
            . '</span>'
            . '</a>';

        echo '<div'
            . ' id="container'
            . $id
            . '"'
            . ' class="bel-debug-content"'
            . ' style="display:'
            . $display
            . ';">';

        foreach ($values as $key => $value) {

            echo '<div class="bel-debug-line">';

            echo '<span class="bel-debug-tree">'
                . '|'
                . '</span>';

            echo '<span class="bel-debug-key">'
                . '['
                . htmlspecialchars((string) $key)
                . ']'
                . '</span>';

            echo '<span class="bel-debug-arrow">'
                . '=>'
                . '</span>';

            self::display(
                $value,
                false,
                $level + 1,
                $collapse
            );

            echo '</div>';
        }

        echo '</div>';

        echo '</div>';
    }

    /**
     * Affiche une valeur simple
     */
    private static function displayValue(
        mixed $data,
        string $type,
        ?int $length,
        bool $isCli
    ): void {
        $value = match ($type) {

            'String' => '"' . htmlspecialchars(
                (string) $data
            ) . '"',

            'Boolean' => $data
                ? 'TRUE'
                : 'FALSE',

            'NULL' => 'NULL',

            'Float' => htmlspecialchars(
                (string) $data
            ),

            'Integer' => htmlspecialchars(
                (string) $data
            ),

            'Resource' => 'RESOURCE',

            'Callable' => 'CALLABLE',

            default => htmlspecialchars(
                print_r($data, true)
            ),
        };

        if ($isCli) {

            echo $type;

            if ($length !== null) {
                echo '(' . $length . ')';
            }

            echo '  ' . $value . PHP_EOL;

            return;
        }

        $class = match ($type) {
            'String'  => 'bel-debug-string',
            'Integer' => 'bel-debug-integer',
            'Float'   => 'bel-debug-float',
            'Boolean' => 'bel-debug-boolean',
            'NULL'    => 'bel-debug-null',
            default   => 'bel-debug-value',
        };

        echo '<span class="bel-debug-type">'
            . htmlspecialchars($type)
            . ($length !== null
                ? '(' . $length . ')'
                : '')
            . '</span>';

        echo '<span class="'
            . $class
            . '">'
            . $value
            . '</span>';
    }

    /**
     * JavaScript de dépliage
     */
    private static function javascript(): void
    {
        static $loaded = false;

        if ($loaded) {
            return;
        }

        $loaded = true;

        echo <<<'HTML'
<script>
window.BelCMSDebug = {

    toggle: function (id) {

        const container =
            document.getElementById('container' + id);

        const plus =
            document.getElementById('plus' + id);

        if (!container) {
            return;
        }

        const hidden =
            container.style.display === 'none';

        container.style.display =
            hidden ? 'inline' : 'none';

        if (plus) {
            plus.innerHTML =
                hidden ? '&#10549;' : '';
        }
    }

};
</script>

<style>
.bel-debug-container {
    font-family: Consolas, monospace;
    font-size: 13px;
    margin: 4px 0;
}

.bel-debug-toggle {
    display: inline-flex;
    gap: 8px;
    color: #e74c3c;
    font-weight: bold;
    text-decoration: none;
}

.bel-debug-content {
    margin-left: 15px;
}

.bel-debug-line {
    margin: 2px 0;
}

.bel-debug-tree {
    color: #e74c3c;
    margin-right: 8px;
}

.bel-debug-key {
    display: inline-block;
    min-width: 100px;
    color: #3498db;
    font-weight: bold;
}

.bel-debug-arrow {
    margin-right: 8px;
}

.bel-debug-type {
    display: inline-block;
    min-width: 90px;
    color: #666;
}

.bel-debug-string {
    color: green;
}

.bel-debug-integer {
    color: red;
}

.bel-debug-float {
    color: #0099c5;
}

.bel-debug-boolean {
    color: #92008d;
    font-weight: bold;
}

.bel-debug-null {
    color: #777;
    font-style: italic;
}

.bel-debug-value {
    color: #333;
}
</style>
HTML;
    }

    /**
     * Indentation terminal
     */
    private static function cliIndent(
        int $level
    ): void {
        echo str_repeat(
            '|    ',
            $level
        );
    }
}