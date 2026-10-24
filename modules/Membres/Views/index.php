<?php
/** @var array $members */
$members = $members ?? [];
$page = max(1, (int) ($page ?? 1));
$pages = max(1, (int) ($pages ?? 1));
$total = max(0, (int) ($total ?? count($members)));
$search = (string) ($search ?? '');

function membres_e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function membres_avatar(?string $avatar): ?string
{
    $avatar = trim((string) $avatar);

    if ($avatar === '') {
        return null;
    }

    if (preg_match('~^(?:https?:)?//~i', $avatar) === 1 || str_starts_with($avatar, '/')) {
        return $avatar;
    }

    return '/' . ltrim($avatar, '/');
}
?>

<div class="members-page">
    <div class="members-header">
        <div>
            <span class="members-eyebrow">COMMUNAUTÉ BEL-CMS</span>
            <h1>Membres</h1>
            <p>Découvrez les membres de la communauté Bel-CMS.</p>
        </div>
        <div class="members-count">
            <strong><?= count($members) ?></strong>
            <span>membre<?= count($members) > 1 ? 's' : '' ?></span>
        </div>
    </div>

    <form class="members-search" method="get" action="/members" role="search">
        <label class="visually-hidden" for="members-search">Rechercher un membre</label>
        <input id="members-search" type="search" name="search" value="<?= membres_e($search) ?>" maxlength="100" placeholder="Rechercher par pseudo…">
        <button type="submit"><i class="fa-solid fa-magnifying-glass"></i> Rechercher</button>
        <?php if ($search !== ''): ?><a href="/members">Effacer</a><?php endif; ?>
    </form>

    <?php if ($members === []): ?>
        <div class="members-empty">
            <i class="fa-solid fa-users-slash"></i>
            <h2>Aucun membre</h2>
            <p>Aucun profil public n'est disponible pour le moment.</p>
        </div>
    <?php else: ?>
        <div class="members-grid">
            <?php foreach ($members as $member): ?>
                <?php
                    $avatar = membres_avatar($member->avatar ?? null);
                    $username = trim((string) ($member->username ?? ''));
                    $country = trim((string) ($member->country ?? ''));
                    $presentation = trim((string) ($member->infos_text ?? ''));
                    if ($presentation !== '') {
                        $presentation = mb_strimwidth($presentation, 0, 150, '…', 'UTF-8');
                    }
                ?>

                <article class="member-card">
                    <div class="member-card-top"></div>

                    <div class="member-card-body">
                        <div class="member-avatar">
                            <?php if ($avatar !== null): ?>
                                <img src="<?= membres_e($avatar) ?>" alt="Avatar de <?= membres_e($username) ?>">
                            <?php else: ?>
                                <i class="fa-solid fa-user"></i>
                            <?php endif; ?>
                        </div>

                        <div class="member-identity">
                            <h2><?= membres_e($username) ?></h2>
                            <?php if ($country !== ''): ?>
                                <span><i class="fa-solid fa-location-dot"></i> <?= membres_e($country) ?></span>
                            <?php endif; ?>
                        </div>

                        <?php if ($presentation !== ''): ?>
                            <p class="member-description"><?= membres_e($presentation) ?></p>
                        <?php else: ?>
                            <p class="member-description member-description-empty">Aucune présentation publique.</p>
                        <?php endif; ?>

                        <a class="member-button" href="/members/profile?hash_key=<?= urlencode((string) $member->hash_key) ?>">
                            Voir le profil
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <?php if ($pages > 1): ?>
            <nav class="members-pagination" aria-label="Pagination des membres">
                <?php if ($page > 1): ?><a href="/members?<?= http_build_query(['search' => $search, 'page' => $page - 1]) ?>" rel="prev">← Précédent</a><?php endif; ?>
                <span>Page <?= $page ?> sur <?= $pages ?></span>
                <?php if ($page < $pages): ?><a href="/members?<?= http_build_query(['search' => $search, 'page' => $page + 1]) ?>" rel="next">Suivant →</a><?php endif; ?>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>
