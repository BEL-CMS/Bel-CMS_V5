<?php
declare(strict_types=1);
namespace BelCMS\Admin\Banishment;
use BelCMS\Core\Assets;
use BelCMS\Core\Security\Banishment as BanishmentService;
use BelCMS\Core\User;
use BelCMS\Core\View;
use Throwable;

final class Controller
{
    public function __construct(private User $user, private View $view, private Assets $assets, private Model $model, private BanishmentService $banishment) {}

    public function index(): void
    {
        if (!$this->authorize()) return;
        $page=max(1,(int)($_GET['page']??1)); $search=trim((string)($_GET['search']??''));
        $status=(string)($_GET['status']??'all'); if(!in_array($status,['all','active','expired','revoked'],true)) $status='all';
        $this->assets->css('/administration/Modules/Banishment/css/banishment.css');
        echo $this->view->renderAdmin('Banishment','index',['stats'=>$this->model->getStats(),'listing'=>$this->model->getBanishments($page,25,$search,$status),'search'=>$search,'status'=>$status,'csrfToken'=>$this->csrfToken(),'message'=>$this->flashMessage()]);
    }

    public function create(): void
    {
        if (!$this->authorize()) {
            return;
        }

        $this->assets->css(
            '/administration/Modules/Banishment/css/banishment.css'
        );

        // Durées disponibles pour le formulaire.
        $durations = $this->availableDurations();

        echo $this->view->renderAdmin(
            'Banishment',
            'create',
            [
                'durations' => $durations,
                'csrfToken' => $this->csrfToken(),
                'error'     => '',
            ]
        );
    }

    public function store(): void
    {
        if(!$this->authorize()) return; if(!$this->verifyCsrf()){http_response_code(403);exit('Jeton CSRF invalide.');}
        $hash=trim((string)($_POST['hash_key']??'')); $ip=trim((string)($_POST['ip_address']??'')); $reason=trim((string)($_POST['reason']??'')); $level=filter_var($_POST['level']??null,FILTER_VALIDATE_INT);
        $hash=$hash===''?null:$hash; $ip=$ip===''?null:$ip;
        if($hash===null && $ip===null){$this->renderCreateError('Indiquez un hash_key, une IP ou les deux.');return;}
        if($reason==='' || mb_strlen($reason,'UTF-8')>500){$this->renderCreateError('Le motif est obligatoire et limité à 500 caractères.');return;}
        if ($level === false || !in_array((int) $level, array_keys($this->availableDurations()), true)) {
            $this->renderCreateError('Durée invalide.');
            return;
        }
        if($ip!==null && filter_var($ip,FILTER_VALIDATE_IP)===false){$this->renderCreateError('Adresse IP invalide.');return;}
        try {
            // Adaptez cet appel si la signature de votre Banishment::ban() diffère.
            $admin=method_exists($this->user,'hashKey')?$this->user->hashKey():null;
            $ok=$this->banishment->ban($hash,$ip,$reason,(int)$level,$admin);
            $this->setFlash($ok===false?'Le bannissement n’a pas été enregistré.':'Bannissement créé avec succès.');
            header('Location: /admin/banishment'); exit;
        } catch(Throwable $e){error_log('[Bel-CMS Banishment] '.$e->getMessage());$this->renderCreateError('Erreur lors de la création. Consultez les logs PHP.');}
    }

    public function revoke(): void
    {
        if(!$this->authorize()) return;
        if(!$this->verifyCsrf()){http_response_code(403);exit('Jeton CSRF invalide.');}

        $id=filter_var($_POST['id']??null,FILTER_VALIDATE_INT);
        $reason=trim((string)($_POST['revoke_reason']??''));
        if(!$id || $id<1){http_response_code(400);exit('Identifiant de bannissement invalide.');}

        if(mb_strlen($reason,'UTF-8')>500){http_response_code(400);exit('Motif trop long.');}

        try {
            $admin=method_exists($this->user,'hashKey')?$this->user->hashKey():null;
            $ok=$this->banishment->revoke((int)$id,$admin,$reason!==''?$reason:null);
            $this->setFlash($ok?'Bannissement révoqué.':'Bannissement introuvable ou déjà révoqué.');
        } catch(Throwable $e){
            error_log('[Bel-CMS Banishment] '.$e->getMessage());
            $this->setFlash('Erreur pendant la révocation. Consultez les logs PHP.');
        }
        header('Location: /admin/banishment'); exit;
    }

    public function logs(): void
    {
        if(!$this->authorize()) return; $this->assets->css('/administration/Modules/Banishment/css/banishment.css');
        echo $this->view->renderAdmin('Banishment','logs',['logs'=>$this->model->getLogs(200)]);
    }

    private function authorize(): bool
    {
        if(!$this->user->isLogged()){header('Location: /user/login');exit;}
        if(!$this->user->isAdmin()){http_response_code(403);echo 'Accès interdit.';return false;} return true;
    }
    private function verifyCsrf(): bool { return function_exists('csrf_verify') && csrf_verify($_POST['csrf_token']??null); }
    private function csrfToken(): string { if(function_exists('csrf_token')) return (string)csrf_token(); if(function_exists('csrf_get_token')) return (string)csrf_get_token(); return ''; }
    private function availableDurations(): array
    {
        return [
            9 => '1 minute',
            8 => '5 minutes',
            7 => '15 minutes',
            6 => '1 heure',
            5 => '1 jour',
            4 => '7 jours',
            3 => '1 mois',
            2 => '6 mois',
            1 => '1 an',
            0 => 'Permanent',
        ];
    }

    private function renderCreateError(string $error): void
    {
        $this->assets->css('/administration/Modules/Banishment/css/banishment.css');
        echo $this->view->renderAdmin('Banishment', 'create', [
            'durations' => $this->availableDurations(),
            'csrfToken' => $this->csrfToken(),
            'error' => $error,
        ]);
    }
    private function setFlash(string $msg): void 
    {
        if(session_status() === PHP_SESSION_ACTIVE) $_SESSION['BELCMS_BANISHMENT_FLASH'] = $msg;
    }
    private function flashMessage(): string 
    {
        if(session_status()!==PHP_SESSION_ACTIVE) 
        return '';
        $m=(string)($_SESSION['BELCMS_BANISHMENT_FLASH']??'');
        unset($_SESSION['BELCMS_BANISHMENT_FLASH']);
        return $m; 
    }
}
