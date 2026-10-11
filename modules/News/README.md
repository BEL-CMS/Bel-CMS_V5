# Bel-CMS V5 - Module News

Module d'actualités compatible avec l'architecture Bel-CMS V5 / PHP 8.5.

## Contenu

- `Controller.php` : contrôleur du module
- `Model.php` : accès aux actualités et catégories
- `routes.php` : routes publiques
- `module.php` : déclaration du module
- `Views/index.php` : liste des actualités
- `Views/category.php` : liste par catégorie
- `Views/show.php` : affichage d'une actualité
- `css/news.css` : styles du module
- `js/news.js` : JavaScript du module
- `langs/lang.fr.php` : traductions françaises
- `install.sql` : création des tables News et catégories

## Routes

- `GET /news`
- `GET /news/view?slug=mon-article`
- `GET /news/category?id_cat=1`
- `POST /news/like`

## Champs utilisés

Le module V5 utilise les nouveaux champs :

- `title`
- `slug`
- `image`
- `content`
- `tags`
- `views`
- `likes`
- `status`
- `created_at`
- `updated_at`
- `hash_key`
- `id_cat`

Les anciennes propriétés V4 (`name`, `rewrite_name`, `img`, `view`, `like_post`, etc.) ne sont plus utilisées par les vues.
