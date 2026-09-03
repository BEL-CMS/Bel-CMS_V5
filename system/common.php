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

if (!defined('CHECK_INDEX')):
	header($_SERVER['SERVER_PROTOCOL'] . ' 403 Direct access forbidden');
	exit('<!doctype html><html><head><meta charset="utf-8"><title>BEL-CMS : Error 403 Forbidden</title><style>h1{margin: 20px auto;text-align:center;color: red;}p{text-align:center;font-weight:bold;</style></head><body><h1>HTTP Error 403 : Forbidden</h1><p>You don\'t permission to access / on this server.</p></body></html>');
endif;

final class Common
{
    #########################################
    #            Transform date             #
    #---------------------------------------#
    #   Le formt à utiliser pour les dates:
    #	IntlDateFormatter::NONE (masque la date)
    #	IntlDateFormatter::SHORT (14/07/2017)
    #	IntlDateFormatter::MEDIUM (14 juil. 2017)
    #	IntlDateFormatter::LONG (14 juillet 2017)
    #	IntlDateFormatter::FULL (vendredi 14 juillet 2017)
    #----------------------------------------#
    #   Le format à utiliser pour l'heure:   #
    #----------------------------------------#
    #	IntlDateFormatter::NONE (masque l'heure)
    #	IntlDateFormatter::SHORT (00:00)
    #	IntlDateFormatter::MEDIUM (à 00:00:00)
    #	IntlDateFormatter::LONG (à 00:00:00 UTC+2)
    #	IntlDateFormatter::FULL (à 00:00:00 heure d’été d’Europe centrale)
    #########################################
    public static function TransformDate ($date, $d = 'NONE', $t = 'NONE')
    {
        # fix empty date - 30-11-0001
        if ($date == '31-11-0001' or $date == '0000-00-00' or $date == '30-11--0001' or empty($date)) {
            return date('Y-m-d');
        }

        if ($_SESSION['CONFIG']['CMS_WEBSITE_LANG'] == constant('FRENCH')) {
            $lg = 'fr_FR';
        } else if ($_SESSION['CONFIG']['CMS_WEBSITE_LANG'] == constant('ENGLISH')) {
            $lg = 'en_US';
        } else if ($_SESSION['CONFIG']['CMS_WEBSITE_LANG'] == constant('NETHERLANDS')) {
            $lg = 'nl_NL';
        } else if ($_SESSION['CONFIG']['CMS_WEBSITE_LANG'] == constant('DEUTCH')) {
            $lg = 'de_DE';
        } else {
            $lg = 'fr_FR';
        }

        $d    = strtoupper($d); $t = strtoupper($t);
        $date = str_replace('/', '-', $date);
        $date = new DateTime($date);

        if ($d == 'NONE' && $t == 'NONE') {
            $return = new IntlDateFormatter($lg, IntlDateFormatter::NONE, IntlDateFormatter::NONE);
        } else if ($d == 'SHORT' && $t == 'NONE') {
            $return = new IntlDateFormatter($lg, IntlDateFormatter::SHORT, IntlDateFormatter::NONE);
        } else if ($d == 'MEDIUM' && $t == 'NONE') {
            $return = new IntlDateFormatter($lg, IntlDateFormatter::MEDIUM, IntlDateFormatter::NONE);
        } else if ($d == 'LONG' && $t == 'NONE') {
            $return = new IntlDateFormatter($lg, IntlDateFormatter::LONG, IntlDateFormatter::NONE);
        } else if ($d == 'FULL' && $t == 'NONE') {
            $return = new IntlDateFormatter($lg, IntlDateFormatter::FULL, IntlDateFormatter::NONE);
        }
        else if ($d == 'NONE' && $t == 'SHORT') {
            $return = new IntlDateFormatter($lg, IntlDateFormatter::NONE, IntlDateFormatter::SHORT);
        } else if ($d == 'SHORT' && $t == 'SHORT') {
            $return = new IntlDateFormatter($lg, IntlDateFormatter::SHORT, IntlDateFormatter::SHORT);
        } else if ($d == 'MEDIUM' && $t == 'SHORT') {
            $return = new IntlDateFormatter($lg, IntlDateFormatter::MEDIUM, IntlDateFormatter::SHORT);
        } else if ($d == 'LONG' && $t == 'SHORT') {
            $return = new IntlDateFormatter($lg, IntlDateFormatter::LONG, IntlDateFormatter::SHORT);
        } else if ($d == 'FULL' && $t == 'SHORT') {
            $return = new IntlDateFormatter($lg, IntlDateFormatter::FULL, IntlDateFormatter::SHORT);
        }
        else if ($d == 'NONE' && $t == 'MEDIUM') {
            $return = new IntlDateFormatter($lg, IntlDateFormatter::NONE, IntlDateFormatter::MEDIUM);
        } else if ($d == 'SHORT' && $t == 'MEDIUM') {
            $return = new IntlDateFormatter($lg, IntlDateFormatter::SHORT, IntlDateFormatter::MEDIUM);
        } else if ($d == 'MEDIUM' && $t == 'MEDIUM') {
            $return = new IntlDateFormatter($lg, IntlDateFormatter::MEDIUM, IntlDateFormatter::MEDIUM);
        } else if ($d == 'LONG' && $t == 'MEDIUM') {
            $return = new IntlDateFormatter($lg, IntlDateFormatter::LONG, IntlDateFormatter::MEDIUM);
        } else if ($d == 'FULL' && $t == 'MEDIUM') {
            $return = new IntlDateFormatter($lg, IntlDateFormatter::FULL, IntlDateFormatter::MEDIUM);
        }
        else if ($d == 'NONE' && $t == 'LONG') {
            $return = new IntlDateFormatter($lg, IntlDateFormatter::NONE, IntlDateFormatter::LONG);
        } else if ($d == 'SHORT' && $t == 'LONG') {
            $return = new IntlDateFormatter($lg, IntlDateFormatter::SHORT, IntlDateFormatter::LONG);
        } else if ($d == 'MEDIUM' && $t == 'LONG') {
            $return = new IntlDateFormatter($lg, IntlDateFormatter::MEDIUM, IntlDateFormatter::LONG);
        } else if ($d == 'LONG' && $t == 'LONG') {
            $return = new IntlDateFormatter($lg, IntlDateFormatter::LONG, IntlDateFormatter::LONG);
        } else if ($d == 'FULL' && $t == 'LONG') {
            $return = new IntlDateFormatter($lg, IntlDateFormatter::FULL, IntlDateFormatter::LONG);
        }
        else if ($d == 'NONE' && $t == 'FULL') {
            $return = new IntlDateFormatter($lg, IntlDateFormatter::NONE, IntlDateFormatter::FULL);
        } else if ($d == 'SHORT' && $t == 'FULL') {
            $return = new IntlDateFormatter($lg, IntlDateFormatter::SHORT, IntlDateFormatter::FULL);
        } else if ($d == 'MEDIUM' && $t == 'FULL') {
            $return = new IntlDateFormatter($lg, IntlDateFormatter::MEDIUM, IntlDateFormatter::FULL);
        } else if ($d == 'LONG' && $t == 'FULL') {
            $return = new IntlDateFormatter($lg, IntlDateFormatter::LONG, IntlDateFormatter::FULL);
        } else if ($d == 'FULL' && $t == 'FULL') {
            $return = new IntlDateFormatter($lg, IntlDateFormatter::FULL, IntlDateFormatter::FULL);
        } else if ($d == 'SQLDATE') {
            $return = new IntlDateFormatter(
                $lg,
                IntlDateFormatter::FULL,
                IntlDateFormatter::FULL,
                'Europe/Brussels',
                IntlDateFormatter::GREGORIAN,
                'yyyy-MM-dd'
            );
        } else if ($d == 'SQLDATETIME') {
            $return = new IntlDateFormatter(
                $lg,
                IntlDateFormatter::FULL,
                IntlDateFormatter::FULL,
                'Europe/Brussels',
                IntlDateFormatter::GREGORIAN,
                'yyyy-MM-dd hh-mm-ss'
            );
        } else {
            $return = new IntlDateFormatter($lg, IntlDateFormatter::FULL, IntlDateFormatter::FULL);
        }

        return $return->format($date);
    }
    #########################################
    # Secure PHP - HTML Var
    #########################################
    public static function VarSecure($data = null, $allowHtml = false, array $options = [])
    {
        if ($data === null) {
            return null;
        }

        $defaults = [
            'max_length' => 10000,
            'allow_empty' => true,
            'custom_tags' => null,
            'preserve_newlines' => false,
            'strict_mode' => true,
            'allow_class' => false
        ];
        $options = array_merge($defaults, $options);

        $allowedTags = $options['custom_tags'] ?? 
            '<table><thead><tbody><tfoot><tr><td><th><colgroup><col>' .
            '<a><abbr><address><article><aside><b><bdi><bdo><blockquote><br>' .
            '<caption><cite><code><data><dd><del><details><dfn><div><dl><dt>' .
            '<em><figcaption><figure><footer><h1><h2><h3><h4><h5><h6>' .
            '<header><hr><i><kbd><li><main><mark><menu><nav><ol><p><pre>' .
            '<q><rp><rt><ruby><s><samp><section><small><span><strong><sub>' .
            '<summary><sup><time><u><ul><var><wbr><label><optgroup><img>';

        $clean = function ($value) use ($allowHtml, $allowedTags, $options) {
            
            if (!is_string($value)) {
                if (is_numeric($value)) {
                    return $value;
                }
                if (is_bool($value)) {
                    return $value;
                }
                return '';
            }

            if (mb_strlen($value, 'UTF-8') > $options['max_length']) {
                $value = mb_substr($value, 0, $options['max_length'], 'UTF-8');
            }

            $value = trim($value);

            if (!$options['allow_empty'] && empty($value)) {
                return '';
            }

            if (!mb_check_encoding($value, 'UTF-8')) {
                $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
            }

            if ($options['preserve_newlines']) {
                $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value);
            } else {
                $value = preg_replace('/[\x00-\x1F\x7F]/u', '', $value);
            }

            $value = preg_replace('/[\x{202E}\x{202D}\x{200E}\x{200F}]/u', '', $value);

            if (class_exists('Normalizer')) {
                $value = \Normalizer::normalize($value, \Normalizer::FORM_C);
            }

            if ($allowHtml) {
                
                $previousValue = '';
                $iterations = 0;

                while ($previousValue !== $value && $iterations < 3) {
                    $previousValue = $value;
                    $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $iterations++;
                }

                $value = strip_tags($value, $allowedTags);

                $value = preg_replace(
                    '/\s*on\w+\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]*)/iu',
                    '',
                    $value
                );

                if ($options['strict_mode']) {

                    $value = preg_replace(
                        '/\s*style\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]*)/iu',
                        '',
                        $value
                    );

                    if (!$options['allow_class']) {
                        $value = preg_replace(
                            '/\s*class\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]*)/iu',
                            '',
                            $value
                        );
                    }
                }

                $value = preg_replace(
                    '/(?:javascript|vbscript|data|file|about)\s*:/iu',
                    '',
                    $value
                );

                $value = preg_replace(
                    '/<\s*(script|iframe|object|embed|applet|meta|link|base)[^>]*>.*?<\s*\/\s*\1\s*>/isu',
                    '',
                    $value
                );

                $value = preg_replace('/<!--.*?-->/s', '', $value);

                $value = preg_replace_callback(
                    '/(href|src)\s*=\s*(["\'])([^"\']*)\2/iu',
                    function($matches) {
                        $url = $matches[3];
                        if (preg_match('/^(?:https?:|mailto:|\/|#|[a-z0-9_\-\.]+)/i', $url)) {
                            return $matches[0];
                        }
                        return '';
                    },
                    $value
                );

                $value = preg_replace('/<([a-z]+)(?![^>]*\/>)[^>]*$/i', '', $value);

            } else {
                $value = htmlspecialchars(
                    $value,
                    ENT_QUOTES | ENT_HTML5 | ENT_SUBSTITUTE,
                    'UTF-8'
                );
            }

            return $value;
        };

        if (is_array($data)) {
            $return = [];
            foreach ($data as $k => $v) {
                $safeKey = is_string($k) ? $clean($k) : $k;
                if (is_array($v)) {
                    $return[$safeKey] = self::VarSecure($v, $allowHtml, $options);
                } else {
                    $return[$safeKey] = $clean($v);
                }
            }
            return $return;
        }

        return $clean($data);
    }
}