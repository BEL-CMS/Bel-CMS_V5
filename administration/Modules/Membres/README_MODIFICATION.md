# Pages de modification des membres — Bel-CMS V5

## Administration
- Liste : `/admin/members`
- Modification : `/admin/members/edit?hash_key=HASH_DU_MEMBRE`
- Routes GET et POST déclarées dans `administration/Modules/Membres/routes.php`.
- Accès réservé aux administrateurs connectés ; POST protégé par `csrf_verify()` et champs autorisés explicitement.

## Profil personnel
- La page existante `/profils/edit` et son formulaire `/profils/editprofils` sont conservés.
- `modules/Profils/Model.php` inclut désormais `updateProfile()` appelé par le contrôleur existant. Le hash de compte est pris depuis la session côté contrôleur, pas depuis le formulaire.

## Champs privés
La nouvelle page d'administration du profil ne sélectionne ni n'affiche le téléphone ou la date de naissance. Les mots de passe, jetons 2FA et autres secrets ne sont jamais modifiables ici.

## À tester
Tester d'abord en local sur une sauvegarde. Vérifier la présence des champs listés dans les tables `TABLE_USERS` et `TABLE_USERS_PROFILS` sur la base utilisée. Aucun changement de schéma SQL n'est requis par le module.
