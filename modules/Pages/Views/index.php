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

use BelCMS\system\Common;

?>
<div id="belcms_module_news">
    <header class="belcms_module_pages_header">
        <h1><?= __('PAGES_TITLE') ?></h1>
        <p>Liste des pages.</p>
    </header>
    <?php if (empty($pages)):?>
        <div class="belcms_module_pages_empty">
            <?= __('NEWS_NO_RESULT') ?>
        </div>
    <?php else: ?>
        <table id="belcms_module_pages_list">
            <thead>
                <tr>
                <th>Nom</th>
                <th>Publié</th>
                <th class="belcms_center">Nombre de pages</th>
                <th class="belcms_center">Nombre de vu</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($pages as $page): ?>
                <tr class="belcms_module_pages_card">
                    <td>
                        <?= htmlspecialchars($page->name) ?>
                    </td>
                    <td><?= Common::TransformDate(htmlspecialchars($page->publish_date), 'MEDIUM', 'MEDIUM'); ?></td>
                    <td class="belcms_center">0</td>
                    <td class="belcms_center">0</td>
                    <td>
                        <a href="/pages/<?= urlencode($page->id_page) ?>" class="belcms_module__pages_link"><?= __('ENTER') ?> →</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>