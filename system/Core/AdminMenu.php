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

final class AdminMenu
{
    public function __construct(private ModuleManager $moduleManager) { }
    /**
     * Retourne les éléments du menu administration.
     */
    public function getItems(): array
    {
        $items = [];
        foreach ($this->moduleManager->all() as $module) {
            $config = $module['config'] ?? [];
            /*
             * -------------------------------------------------
             * Administration activée ?
             * -------------------------------------------------
             */
            if (
                ($config['admin']['enabled'] ?? false) !== true
            ) {
                continue;
            }
            /*
             * -------------------------------------------------
             * Menu activé ?
             * -------------------------------------------------
             */
            $menu = $config['admin']['menu'] ?? [];
            if (
                !is_array($menu)
                || ($menu['enabled'] ?? false) !== true
            ) {
                continue;
            }
            /*
             * -------------------------------------------------
             * Informations du menu
             * -------------------------------------------------
             */
            $items[] = [
                'module' => $module['name'],
                'label' => $menu['label']
                    ?? $module['name'],
                'icon' => $menu['icon']
                    ?? 'fa-puzzle-piece',
                'order' => (int) (
                    $menu['order'] ?? 100
                ),
            ];
        }
        /*
         * -----------------------------------------------------
         * Tri du menu
         * -----------------------------------------------------
         */
        usort(
            $items,
            static function (
                array $a,
                array $b
            ): int {
                return $a['order'] <=> $b['order'];
            }
        );
        return $items;
    }
}