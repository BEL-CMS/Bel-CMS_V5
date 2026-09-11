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

use BelCMS\Core\BDD;

if (!defined('CHECK_INDEX')):
    header($_SERVER['SERVER_PROTOCOL'] . ' 403 Direct access forbidden');
    exit('<!doctype html><html><head><meta charset="utf-8"><title>BEL-CMS : Error 403 Forbidden</title><style>h1{margin: 20px auto;text-align:center;color: red;}p{text-align:center;font-weight:bold;</style></head><body><h1>HTTP Error 403 : Forbidden</h1><p>You don\'t permission to access / on this server.</p></body></html>');
endif;

################################################
# Class du CMS Captcha
################################################
class Captcha
{
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start([
                'cookie_httponly' => true,
                'cookie_secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
                'cookie_samesite' => 'Strict',
            ]);
        }
        self::cleanBlacklist();
    }

    private static function ip()
    {
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    private static function now()
    {
        return time();
    }

    private static function setError($message, $type = 'error')
    {
        $_SESSION['CAPTCHA_ERROR'] = [
            'type'    => $type,
            'message' => $message,
            'time'    => self::now()
        ];
    }

    public static function getLastError()
    {
        return $_SESSION['CAPTCHA_ERROR'] ?? null;
    }

    public static function clearError(): void
    {
        unset($_SESSION['CAPTCHA_ERROR']);
    }

    private static function log($type, $message = '')
    {
        try {

            $sql = new BDD;
            $sql->table('TABLE_CAPTCHA_LOGS');
            $sql->insert([
                'ip'         => self::ip(),
                'type'       => $type,
                'message'    => $message,
                'created_at' => self::now()
            ]);

        } catch (\Throwable $e) {
            // ignore log errors
        }
    }

    public function createCaptcha()
    {
        $a = rand(1, 9);
        $b = rand(1, 9);

        $_SESSION['CAPTCHA'] = [
            'question' => $a . ' + ' . $b,
            'result'   => $a + $b
        ];

        return $_SESSION['CAPTCHA'];
    }

    private static function getData()
    {
        $sql = new BDD;
        $sql->table('TABLE_BELCMS_SHIELD');
        $sql->where([
            ['name' => 'ip', 'value' => self::ip()]
        ]);
        $sql->queryOne();

        return $sql->data ?? null;
    }

    private static function save(array $data)
    {
        $sql = new BDD;
        $sql->table('TABLE_BELCMS_SHIELD');
        $sql->where([
            ['name' => 'ip', 'value' => self::ip()]
        ]);
        $sql->update($data);
    }

    private static function createShieldRow()
    {
        $sql = new BDD;
        $sql->table('TABLE_BELCMS_SHIELD');
        $sql->insert([
            'ip'            => self::ip(),
            'success'       => 0,
            'attempts'      => 0,
            'blocked_until' => 0,
            'last_action'   => self::now()
        ]);
    }

    public static function isBlocked()
    {
        $sql = new BDD;
        $sql->table('TABLE_CAPTCHA_BLACKLIST');
        $sql->where([
            ['name' => 'ip', 'value' => self::ip()]
        ]);
        $sql->queryOne();

        $data = $sql->data ?? null;

        if ($data && (int)$data->blocked_until > self::now()) {

            self::setError(
                'Trop de tentatives. Réessayez dans quelques minutes.',
                'warning'
            );

            return true;
        }

        return false;
    }

    private static function block($reason, $minutes = 0)
    {
        $sql = new BDD;
        $sql->table('TABLE_CAPTCHA_BLACKLIST');
        $sql->insert([
            'ip'            => self::ip(),
            'reason'        => $reason,
            'created_at'    => self::now(),
            'blocked_until' => self::now() + ($minutes * 60)
        ]);

        self::log('blocked', $reason);
    }

    private static function checkFlood()
    {
        $data = self::getData();

        if (!$data) {

            self::createShieldRow();
            return true;
        }

        $now  = self::now();
        $last = (int)$data->last_action;

        if (($now - $last) < 3) {

            self::log('flood', 'Requête trop rapide');

            self::setError(
                'Veuillez attendre quelques secondes avant de réessayer.'
            );

            return false;
        }

        self::save([
            'last_action' => $now
        ]);

        return true;
    }

    private static function fail()
    {
        $data = self::getData();

        if (!$data) {
            self::createShieldRow();
            $data = self::getData();
        }

        $attempts = (int)$data->attempts + 1;

        $update = [
            'attempts'    => $attempts,
            'last_action' => self::now()
        ];

        if ($attempts >= 5) {

            $update['attempts'] = 0;

            self::block(
                'Trop de tentatives de captcha infructueuses',
                5
            );

            self::setError(
                'Trop de tentatives incorrectes. Accès bloqué 5 minutes.',
                'warning'
            );
        }

        self::save($update);
    }

    private static function success()
    {
        $data = self::getData();

        if (!$data) {
            self::createShieldRow();
            $data = self::getData();
        }

        self::save([
            'success'     => ((int)$data->success + 1),
            'attempts'    => 0,
            'last_action' => self::now()
        ]);

        self::log('success', 'Captcha validé');

        return true;
    }

    /**
     * Supprime les doublons de la blacklist.
     * Conserve uniquement l'enregistrement le plus récent.
     */
    private static function cleanBlacklist()
    {
        $sql = new BDD;

        // Récupère toutes les entrées de cette IP
        $sql->table('TABLE_CAPTCHA_BLACKLIST');
        $sql->where([
            ['name' => 'ip', 'value' => self::ip()]
        ]);
        $sql->queryAll();

        if (empty($sql->data) || count($sql->data) <= 1) {
            return;
        }

        // Trie par date décroissante
        usort($sql->data, function ($a, $b) {
            return $b->created_at <=> $a->created_at;
        });

        // Conserve la première
        $keep = array_shift($sql->data);

        // Supprime toutes les autres
        foreach ($sql->data as $row) {

            $delete = new BDD;
            $delete->table('TABLE_CAPTCHA_BLACKLIST');
            $delete->where([
                ['name' => 'id', 'value' => $row->id]
            ]);
            $delete->delete();
        }
    }

    public static function verify()
    {
        if (self::isBlocked()) {
            return false;
        }

        if (!self::checkFlood()) {
            return false;
        }

        if (!empty($_POST['captcha_value'])) {

            self::log('honeypot', 'Bot détecté');
            self::fail();

            self::setError(
                'Comportement suspect détecté.'
            );

            return false;
        }

        $slider = (int)($_POST['belcms_captcha_value'] ?? 0);

        if ($slider < 25 || $slider > 85) {

            self::log('slider', 'Slider hors zone');

            self::fail();

            self::setError(
                'Veuillez positionner le curseur dans la zone autorisée.'
            );

            return false;
        }

        if (!isset($_SESSION['CAPTCHA']['result'])) {

            self::setError(
                'Le captcha a expiré.'
            );

            return false;
        }

        $answer = (int)($_POST['captcha'] ?? 0);

        if ($answer !== (int)$_SESSION['CAPTCHA']['result']) {

            self::log('captcha', 'Mauvaise réponse');

            self::fail();

            self::setError(
                'La réponse au calcul est incorrecte.'
            );

            return false;
        }

        unset($_SESSION['CAPTCHA']);

        return self::success();
    }

    public static function getStopMsg ()
    {
        $sql = new BDD;
        $sql->table('TABLE_CAPTCHA_BLACKLIST');
        $sql->where([
            ['name' => 'ip', 'value' => self::ip()]
        ]);
        $sql->count();
        $data = $sql->data;

        if ($data >= 1) {
            return false;
        } else {
            return true;
        }

    }
}