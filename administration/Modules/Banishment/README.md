# Bel-CMS V5 — Administration Banishment

Module d'administration de bannissement : liste avec recherche/filtre, statistiques, création, révocation et historique.

## Intégration
1. Copiez `administration/Modules/Banishment/` dans le dossier correspondant du projet.
2. Vérifiez dans `config/tables.php` :
   ```php
   'TABLE_BANISHMENT'      => $DB_PREFIX . 'banishment',
   'TABLE_BANISHMENT_LOGS' => $DB_PREFIX . 'banishment_logs',
   ```
3. Vérifiez que l'autoloader prend en charge `BelCMS\\Admin\\Banishment` et que `Application` charge les `routes.php` des modules.
4. Contrôlez les noms des fonctions CSRF dans `Controller.php` (`csrf_token()` / `csrf_verify()`).
5. Comparez les signatures réelles de `Banishment::ban()` et `Banishment::revoke()` avant d'utiliser les formulaires : les appels supposent respectivement `ban(?string $hashKey, ?string $ipAddress, string $reason, int $level, ?string $createdBy): bool` et `revoke(int $id, ?string $adminHashKey, ?string $reason): bool`.

**Important :** ce paquet ne remplace pas `system/Core/Security/Banishment.php`, qui fonctionne déjà chez vous. Testez d'abord en environnement de développement et n'essayez pas de vous bannir vous-même par IP.
