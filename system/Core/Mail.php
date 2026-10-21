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

use BelCMS\Core\BDD;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!defined('CHECK_INDEX')):
	header($_SERVER['SERVER_PROTOCOL'] . ' 403 Direct access forbidden');
	exit('<!doctype html><html><head><meta charset="utf-8"><title>BEL-CMS : Error 403 Forbidden</title><style>h1{margin: 20px auto;text-align:center;color: red;}p{text-align:center;font-weight:bold;</style></head><body><h1>HTTP Error 403 : Forbidden</h1><p>You don\'t permission to access / on this server.</p></body></html>');
endif;

require_once dirname(__DIR__). DIRECTORY_SEPARATOR.'PHPMailer/phpmailer.lang-fr.php';
require_once dirname(__DIR__). DIRECTORY_SEPARATOR.'PHPMailer/Exception.php';
require_once dirname(__DIR__). DIRECTORY_SEPARATOR.'PHPMailer/PHPMailer.php';
require_once dirname(__DIR__). DIRECTORY_SEPARATOR.'PHPMailer/SMTP.php';

final class Mail
{
    private object $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);
        $this->mail->isSMTP();
        self::mailConfig();
    }

    private array $data = [];

    public function getInfosMail () : array
    {
        $data = array();

        foreach (self::db() as $key => $value) {
            $data[$value->name] = $value->value;
        }

        return $data;
    }

    private function db () : array
    {
        $db = new BDD;
		$db->table('TABLE_CONFIG_MAIL');
		$db->queryAll();
        return $db->data;
    }

    private function mailConfig ()
    {
        $data = self::getInfosMail();

        $this->mail->Host       = $data['host'];
        $this->mail->CharSet    = $data['charset'];
        $this->mail->SMTPAuth   = true;
        $this->mail->Username   = $data['username'];
        $this->mail->Password   = $data['password'];
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port       = $data['port'];
        $this->mail->setFrom($data['sender_email'], $data['sender_name']);
        $this->mail->isHTML(true);
    }

    public function addAdress ($mail = null)
    {
        $this->mail->addAddress($mail);
    }

    public function subject ($data = null)
    {
        $this->mail->Subject = $data;
    }

    public function body ($data = null)
    {
        $this->mail->Body = $data;
    }

    public function submit ()
    {
        try {
            return $this->mail->send();
        } catch (Exception $e) {
            echo "Erreur : {$this->mail->ErrorInfo}";
        }
    }
}