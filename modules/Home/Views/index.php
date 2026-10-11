<?php

declare(strict_types=1);

/**
 * Bel-CMS V5
 * Module Home
 */

$title = $title ?? 'Accueil';
$news  = $news ?? [];
$stats = $stats ?? [
    'users'    => 0,
    'profiles' => 0,
    'groups'   => 0,
    'news'     => 0,
];

?>

<div class="home-page">

    <!-- =========================================================
         HERO
    ========================================================== -->
    <section class="home-hero">

        <div class="home-hero-background"></div>
        <div class="home-hero-grid"></div>

        <div class="home-container home-hero-content">

            <div class="home-hero-badge">
                <span class="home-status-dot"></span>
                Bel-CMS V5
            </div>

            <h1>
                Créez votre site avec
                <span>Bel-CMS</span>
            </h1>

            <p class="home-hero-text">
                Un système de gestion de contenu moderne, flexible et sécurisé,
                conçu pour créer et administrer facilement votre site web.
            </p>

            <div class="home-hero-actions">

                <a
                    href="/news"
                    class="home-btn home-btn-primary"
                >
                    <i class="fa-solid fa-newspaper"></i>
                    Découvrir les actualités
                </a>

                <a
                    href="#features"
                    class="home-btn home-btn-secondary"
                >
                    <i class="fa-solid fa-arrow-down"></i>
                    Découvrir Bel-CMS
                </a>

            </div>

        </div>

    </section>


    <!-- =========================================================
         INTRODUCTION
    ========================================================== -->
    <section
        class="home-section home-intro"
        id="features"
    >

        <div class="home-container">

            <div class="home-section-header">

                <span class="home-section-label">
                    <i class="fa-solid fa-code"></i>
                    BEL-CMS
                </span>

                <h2>
                    Un CMS pensé pour
                    <span>évoluer avec vous</span>
                </h2>

                <p>
                    Bel-CMS V5 repose sur une architecture moderne et modulaire
                    permettant d'ajouter facilement de nouvelles fonctionnalités
                    à votre site.
                </p>

            </div>


            <div class="home-feature-grid">

                <!-- Architecture -->
                <article class="home-feature-card">

                    <div class="home-feature-icon">
                        <i class="fa-solid fa-cubes"></i>
                    </div>

                    <h3>
                        Architecture modulaire
                    </h3>

                    <p>
                        Chaque fonctionnalité est organisée dans son propre
                        module afin de garder un système propre et évolutif.
                    </p>

                </article>


                <!-- Sécurité -->
                <article class="home-feature-card">

                    <div class="home-feature-icon">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>

                    <h3>
                        Sécurité
                    </h3>

                    <p>
                        Bel-CMS intègre différentes protections pour sécuriser
                        les utilisateurs et les données de votre site.
                    </p>

                </article>


                <!-- Performance -->
                <article class="home-feature-card">

                    <div class="home-feature-icon">
                        <i class="fa-solid fa-gauge-high"></i>
                    </div>

                    <h3>
                        Performance
                    </h3>

                    <p>
                        Une architecture légère permettant de conserver
                        d'excellentes performances et une navigation rapide.
                    </p>

                </article>


                <!-- PHP -->
                <article class="home-feature-card">

                    <div class="home-feature-icon">
                        <i class="fa-brands fa-php"></i>
                    </div>

                    <h3>
                        PHP moderne
                    </h3>

                    <p>
                        Bel-CMS V5 est développé avec les technologies PHP
                        modernes et une architecture orientée objet.
                    </p>

                </article>

            </div>

        </div>

    </section>


    <!-- =========================================================
         ACTUALITÉS
    ========================================================== -->
    <section class="home-section home-news">

        <div class="home-container">

            <div class="home-section-header">

                <span class="home-section-label">
                    <i class="fa-solid fa-newspaper"></i>
                    ACTUALITÉS
                </span>

                <h2>
                    Les dernières
                    <span>actualités</span>
                </h2>

                <p>
                    Découvrez les dernières nouvelles et mises à jour
                    de Bel-CMS.
                </p>

            </div>


            <?php if (!empty($news)): ?>

                <div class="home-news-grid">

                    <?php foreach ($news as $article): ?>

                        <article class="home-news-card">

                            <?php if (!empty($article->image)): ?>

                                <div class="home-news-image">

                                    <img
                                        src="<?= htmlspecialchars(
                                            (string) $article->image,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>"
                                        alt="<?= htmlspecialchars(
                                            (string) $article->title,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>"
                                        loading="lazy"
                                    >

                                </div>

                            <?php else: ?>

                                <div class="home-news-image home-news-image-empty">

                                    <i class="fa-solid fa-newspaper"></i>

                                </div>

                            <?php endif; ?>


                            <div class="home-news-content">

                                <div class="home-news-meta">

                                    <span>
                                        <i class="fa-regular fa-calendar"></i>

                                        <?=
                                        !empty($article->created_at)
                                            ? htmlspecialchars(
                                                date(
                                                    'd/m/Y',
                                                    strtotime(
                                                        (string) $article->created_at
                                                    )
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            )
                                            : ''
                                        ?>
                                    </span>

                                </div>


                                <h3>
                                    <?= htmlspecialchars(
                                        (string) $article->title,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>
                                </h3>


                                <?php if (!empty($article->tags)): ?>

                                    <div class="home-news-tags">

                                        <?= htmlspecialchars(
                                            (string) $article->tags,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>

                                    </div>

                                <?php endif; ?>


                                <a
                                    href="/news/view?slug=<?= urlencode(
                                        (string) $article->slug
                                    ); ?>"
                                    class="home-news-link"
                                >
                                    Lire l'article

                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>


                <div class="home-news-more">

                    <a
                        href="/news"
                        class="home-btn home-btn-secondary"
                    >
                        <i class="fa-solid fa-newspaper"></i>
                        Voir toutes les actualités
                    </a>

                </div>


            <?php else: ?>

                <div class="home-news-empty">

                    <i class="fa-regular fa-newspaper"></i>

                    <h3>
                        Aucune actualité
                    </h3>

                    <p>
                        Il n'y a actuellement aucune actualité publiée.
                    </p>

                </div>

            <?php endif; ?>

        </div>

    </section>


    <!-- =========================================================
         STATISTIQUES
    ========================================================== -->
    <section class="home-section home-stats">

        <div class="home-container">

            <div class="home-section-header home-stats-header">

                <span class="home-section-label">
                    <i class="fa-solid fa-chart-line"></i>
                    BEL-CMS EN CHIFFRES
                </span>

                <h2>
                    Une plateforme
                    <span>en évolution</span>
                </h2>

            </div>


            <div class="home-stats-grid">

                <!-- Membres -->
                <div class="home-stat">

                    <div class="home-stat-icon">
                        <i class="fa-solid fa-users"></i>
                    </div>

                    <strong>
                        <?= number_format(
                            (int) $stats['users'],
                            0,
                            ',',
                            ' '
                        ); ?>
                    </strong>

                    <span>
                        Membres
                    </span>

                </div>


                <!-- Profils -->
                <div class="home-stat">

                    <div class="home-stat-icon">
                        <i class="fa-solid fa-id-card"></i>
                    </div>

                    <strong>
                        <?= number_format(
                            (int) $stats['profiles'],
                            0,
                            ',',
                            ' '
                        ); ?>
                    </strong>

                    <span>
                        Profils
                    </span>

                </div>


                <!-- Groupes -->
                <div class="home-stat">

                    <div class="home-stat-icon">
                        <i class="fa-solid fa-user-group"></i>
                    </div>

                    <strong>
                        <?= number_format(
                            (int) $stats['groups'],
                            0,
                            ',',
                            ' '
                        ); ?>
                    </strong>

                    <span>
                        Groupes
                    </span>

                </div>


                <!-- News -->
                <div class="home-stat">

                    <div class="home-stat-icon">
                        <i class="fa-solid fa-newspaper"></i>
                    </div>

                    <strong>
                        <?= number_format(
                            (int) $stats['news'],
                            0,
                            ',',
                            ' '
                        ); ?>
                    </strong>

                    <span>
                        Actualités
                    </span>

                </div>

            </div>

        </div>

    </section>


    <!-- =========================================================
         TECHNOLOGIES
    ========================================================== -->
    <section class="home-section home-technologies">

        <div class="home-container">

            <div class="home-tech-box">

                <div class="home-tech-content">

                    <span class="home-section-label">
                        <i class="fa-solid fa-microchip"></i>
                        TECHNOLOGIES
                    </span>

                    <h2>
                        Une base moderne pour
                        <span>vos projets</span>
                    </h2>

                    <p>
                        Bel-CMS V5 est conçu pour fonctionner avec un environnement
                        web moderne et offrir une base solide pour vos futurs projets.
                    </p>

                </div>


                <div class="home-tech-list">

                    <div class="home-tech-item">
                        <i class="fa-brands fa-php"></i>
                        <span>PHP 8.5</span>
                    </div>

                    <div class="home-tech-item">
                        <i class="fa-solid fa-database"></i>
                        <span>MySQL</span>
                    </div>

                    <div class="home-tech-item">
                        <i class="fa-brands fa-html5"></i>
                        <span>HTML5</span>
                    </div>

                    <div class="home-tech-item">
                        <i class="fa-brands fa-css3-alt"></i>
                        <span>CSS3</span>
                    </div>

                    <div class="home-tech-item">
                        <i class="fa-brands fa-js"></i>
                        <span>JavaScript</span>
                    </div>

                    <div class="home-tech-item">
                        <i class="fa-solid fa-cubes"></i>
                        <span>Architecture modulaire</span>
                    </div>

                </div>

            </div>

        </div>

    </section>


    <!-- =========================================================
         CALL TO ACTION
    ========================================================== -->
    <section class="home-section home-cta">

        <div class="home-container">

            <div class="home-cta-box">

                <div class="home-cta-icon">
                    <i class="fa-solid fa-rocket"></i>
                </div>

                <div class="home-cta-content">

                    <h2>
                        Prêt à construire votre projet ?
                    </h2>

                    <p>
                        Découvrez l'univers Bel-CMS et commencez à construire
                        votre propre site web.
                    </p>

                </div>

                <div class="home-cta-action">

                    <a
                        href="/news"
                        class="home-btn home-btn-primary"
                    >
                        <i class="fa-solid fa-arrow-right"></i>
                        Commencer
                    </a>

                </div>

            </div>

        </div>

    </section>

</div>