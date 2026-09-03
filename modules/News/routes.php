<?php

declare(strict_types=1);

use BelCMS\Modules\News\Controller;

return [
    'GET /' => [Controller::class, 'index'],
    'GET /news' => [Controller::class, 'index'],
    'GET /news/categorie/{category}' => [Controller::class, 'category'],
    'GET /news/{rewrite}' => [Controller::class, 'show'],
];