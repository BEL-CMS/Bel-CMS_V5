<?php
declare(strict_types=1);
$account = $account ?? null; $profile = $profile ?? null; $hashKey = (string)($hashKey ?? '');
$errors = is_array($errors ?? null) ? $errors : []; $success = $success ?? null;
$e = static fn(mixed $v): string => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$val = static fn(string $key, string $default = ''): string => (string)($profile->$key ?? $default);
?>
<section class="admin-members admin-members-edit">
  <header class="admin-members__header"><div><span class="admin-members__eyebrow">BEL-CMS V5 · ADMINISTRATION</span><h1>Modifier un membre</h1><p>Compte : <?= $e($account->username ?? '') ?> · Profil public et accès.</p></div><a class="admin-members__action" href="/admin/members">← Retour à la liste</a></header>
  <?php if ($errors !== []): ?><div class="members-edit-alert is-error" role="alert"><?php foreach ($errors as $error): ?><p><?= $e($error) ?></p><?php endforeach; ?></div><?php endif; ?>
  <?php if ($success): ?><div class="members-edit-alert is-success" role="status"><?= $e($success) ?></div><?php endif; ?>
  <form class="members-edit-form" action="/admin/members/edit?hash_key=<?= rawurlencode($hashKey) ?>" method="post">
    <?= $csrf ?? '' ?>
    <input type="hidden" name="hash_key" value="<?= $e($hashKey) ?>">
    <section class="members-edit-card"><h2><i class="fa-solid fa-user-shield"></i> Compte</h2>
      <label>Pseudo<input name="username" required minlength="3" maxlength="100" value="<?= $e($account->username ?? '') ?>"></label>
      <label>E-mail du compte<input type="email" name="email" required maxlength="190" value="<?= $e($account->email ?? '') ?>"></label>
      <label class="members-edit-check"><input type="checkbox" name="valid" value="1" <?= (int)($account->valid ?? 0) === 1 ? 'checked' : '' ?>> Compte actif / validé</label>
    </section>
    <section class="members-edit-card"><h2><i class="fa-solid fa-id-card"></i> Profil public</h2>
      <div class="members-edit-grid">
        <label>Nom affiché<input name="last_name" maxlength="120" value="<?= $e($val('last_name')) ?>"></label>
        <label>Genre<input name="gender" maxlength="40" value="<?= $e($val('gender')) ?>"></label>
        <label>Pays<input name="country" maxlength="100" value="<?= $e($val('country')) ?>"></label>
        <label>E-mail public (facultatif)<input type="email" name="public_mail" maxlength="190" value="<?= $e($val('public_mail')) ?>"></label>
        <label>Site web<input type="url" name="websites" maxlength="255" placeholder="https://…" value="<?= $e($val('websites')) ?>"></label>
        <label>Avatar (URL)<input type="url" name="avatar" maxlength="255" placeholder="https://…" value="<?= $e($val('avatar')) ?>"></label>
        <label>Image de couverture (URL)<input type="url" name="cover_avatar" maxlength="255" placeholder="https://…" value="<?= $e($val('cover_avatar')) ?>"></label>
        <label>Gravatar / identifiant<input name="gravatar" maxlength="255" value="<?= $e($val('gravatar')) ?>"></label>
      </div>
      <label>Présentation<textarea name="infos_text" rows="6" maxlength="5000"><?= $e($val('infos_text')) ?></textarea></label>
      <label>Informations complémentaires<textarea name="profils" rows="4" maxlength="3000"><?= $e($val('profils')) ?></textarea></label>
      <p class="members-edit-note"><i class="fa-solid fa-lock"></i> Le téléphone et la date de naissance ne sont pas affichés ni modifiables depuis cette page.</p>
    </section>
    <div class="members-edit-actions"><button type="submit"><i class="fa-solid fa-floppy-disk"></i> Enregistrer les modifications</button><a href="/admin/members">Annuler</a></div>
  </form>
</section>
<style>
.admin-members-edit .members-edit-form{display:grid;gap:18px}.members-edit-card{padding:22px;border:1px solid var(--bs-border-color,#29445d);border-radius:14px;background:var(--bs-tertiary-bg,rgba(10,24,40,.35));display:grid;gap:16px}.members-edit-card h2{margin:0 0 4px;font-size:1.15rem}.members-edit-card label{display:grid;gap:7px;font-weight:650}.members-edit-card input:not([type=checkbox]),.members-edit-card textarea{width:100%;min-width:0;padding:11px 12px;border:1px solid var(--bs-border-color,#29445d);border-radius:8px;background:var(--bs-body-bg,#0b1724);color:var(--bs-body-color,#e8f3ff);font:inherit}.members-edit-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:15px}.members-edit-card .members-edit-check{display:flex;align-items:center;gap:9px}.members-edit-note{margin:0;color:var(--bs-secondary-color,#8397aa);font-size:.9rem}.members-edit-actions{display:flex;align-items:center;gap:14px}.members-edit-actions button{padding:11px 17px;border:0;border-radius:8px;background:#1677ff;color:white;font-weight:750;cursor:pointer}.members-edit-actions a{color:#168cff;text-decoration:none}.members-edit-alert{padding:14px 16px;margin:0 0 18px;border-radius:10px}.members-edit-alert p{margin:0 0 5px}.members-edit-alert.is-error{border:1px solid #dc3545;background:rgba(220,53,69,.1)}.members-edit-alert.is-success{border:1px solid #198754;background:rgba(25,135,84,.1)}
@media(max-width:650px){.members-edit-grid{grid-template-columns:1fr}.members-edit-card{padding:15px}.members-edit-actions{align-items:stretch;flex-direction:column}}
</style>
