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

namespace BelCMS\Admin\Logs;

if (!defined('CHECK_INDEX')):
	header($_SERVER['SERVER_PROTOCOL'] . ' 403 Direct access forbidden');
	exit('<!doctype html><html><head><meta charset="utf-8"><title>BEL-CMS : Error 403 Forbidden</title><style>h1{margin: 20px auto;text-align:center;color: red;}p{text-align:center;font-weight:bold;</style></head><body><h1>HTTP Error 403 : Forbidden</h1><p>You don\'t permission to access / on this server.</p></body></html>');
endif;

use BelCMS\Core\Assets;
use BelCMS\Core\Csrf;
use BelCMS\Core\Errors\ErrorPage;
use BelCMS\Core\View;
use BelCMS\Core\User;
use BelCMS\system\Common;
use InvalidArgumentException;

final class Controller
{
    public function __construct(
        private View $view,
        private Assets $assets,
        private Model $model,
        private Csrf $csrf,
        private ErrorPage $errorPage,
        private User $user
    ) {
    }
    /**
     * Liste des groupes
     */
    public function index(): void
    {
        if (!$this->user->isLogged()) {
            header('Location: /user/login');
            exit;
        }

        if (!$this->user->isAdmin()) {
            $this->errorPage->show(
                403,
                'Administration',
                'Accès interdit'
            );
        }
        $this->assets->css(
            '/administration/Modules/News/css/news.css'
        );
        $this->assets->css(
            '/administration/Modules/Logs/css/logs.css'
        );
        $this->assets->css(
            '/assets/plugins/datatables/datatables.min.css'
        );
        $this->assets->css(
            '/assets/plugins/datatables/belcms-datatables.css'
        );
        $this->assets->css(
            '/assets/plugins/tooltip/scale.css'
        );
        $this->assets->js(
            '/assets/plugins/datatables/datatables.min.js'
        );
        $this->assets->js(
            '/assets/plugins/tooltip/popper.min.js'
        );
        $this->assets->js(
            '/assets/plugins/tooltip/tippy-bundle.umd.min.js'
        );
        $this->assets->js(
            '/assets/plugins/tooltip/tooltip.js'
        );
 
        $logs = $this->model->getAllLogs();

        foreach ($logs as $key => $value) {
            if ($this->model->isBanned(null, $value->ip)) {
                $logs[$key]->isban = (bool) true;
            } else {
                $logs[$key]->isban = (bool) false;
            }
        }

        echo $this->view->renderAdmin(
            'Logs',
            'index',
            [
                'logs' => $logs,
                'csrf' => $this->csrf->field()
            ]
        );
    }

    public function ban ()
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            if (!csrf_verify($_POST['csrf_token'] ?? null)) {
                $this->errorPage->show(
                    400,
                    'Administration',
                    'Jeton CSRF invalide.'
                );
            }

            $reason = mb_substr('Ban automatique', 0, 500);
            $now = gmdate('Y-m-d H:i:s');
            $expiresAt = $this->expirationDate(0);
            $banishmentKey = bin2hex(random_bytes(16));

            if ($_POST['ip'] !== null && filter_var($_POST['ip'],FILTER_VALIDATE_IP) === false) {
                throw new \Exception('Adresse IP invalide.');
            }

            $ipAddress = $_POST['ip'];
            if (!preg_match('/^[a-zA-Z0-9]{32}$/', $_POST['hashKey'])) {
                throw new InvalidArgumentException(
                    'Identifiant hash_key invalide.'
                );
            } else {
                $hashKey = $_POST['hashKey'];
            }
            $data_insert = [
                'banishment_key' => $banishmentKey,
                'hash_key'       => $hashKey,
                'ip_address'     => $ipAddress,
                'reason'         => $reason,
                'level'          => 0,
                'created_by'     => constant('HASH_KEY_USER'),
                'created_at'     => $now,
                'expires_at'     => $expiresAt,
                'revoked_at'     => null,
                'revoked_by'     => null,
                'revoke_reason'  => null,
            ];

            if ($this->model->addban($data_insert) === true) {
                header("Refresh:0");
                exit;
            } else {
                throw new \Exception('Une erreur est survenue lors du bannissement en BDD');
            }

        }
    }
    private function expirationDate(int $level): ?string
    {
        if ($level === 0) {
            return null;
        }

        $duration = [0];

        $unit = array_key_first($duration);
        $amount = $duration[$unit];

        $date = new \DateTimeImmutable(
            'now',
            new \DateTimeZone('UTC')
        );

        return $date
            ->modify(sprintf('+%d %s', $amount, $unit))
            ->format('Y-m-d H:i:s');
    }
}