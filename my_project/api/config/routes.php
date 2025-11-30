<?php 

return [
    // MainController
    [
        'method' => 'GET',
        'path' => '/',
        'controller' => [\App\Controller\MainController::class, 'welcome'],
    ],
    [
        'method' => 'GET',
        'path' => '/about',
        'controller' => [\App\Controller\MainController::class, 'about'],
    ],
    [
        'method' => 'GET',
        'path' => '/terms',
        'controller' => [\App\Controller\MainController::class, 'terms'],
    ],
    // LoginController
    [
        'method' => 'GET',
        'path' => '/login',
        'controller' => [\App\Controller\LoginController::class, 'index'],
    ],
    [
        'method' => 'POST',
        'path' => '/login',
        'controller' => [\App\Controller\LoginController::class, 'login'],
    ],
    [
        'method' => 'POST',
        'path' => '/refresh_token',
        'controller' => [\App\Controller\LoginController::class, 'refreshToken'],
    ],
    // RecoveryController
    [
        'method' => 'GET',
        'path' => '/recovery',
        'controller' => [\App\Controller\RecoveryController::class, 'index'],
    ],
    [
        'method' => 'POST',
        'path' => '/recovery',
        'controller' => [\App\Controller\RecoveryController::class, 'recovery'],
    ],
    [
        'method' => 'GET',
        'path' => '/recovery/password',
        'controller' => [\App\Controller\RecoveryController::class, 'show'],
    ],
    [
        'method' => 'POST',
        'path' => '/recovery/password',
        'controller' => [\App\Controller\RecoveryController::class, 'confirm'],
    ],
    // RegisterController
    [
        'method' => 'GET',
        'path' => '/register',
        'controller' => [\App\Controller\RegisterController::class, 'index'],
    ],
    [
        'method' => 'POST',
        'path' => '/register',
        'controller' => [\App\Controller\RegisterController::class, 'register'],
    ],
    // ConfirmController
    [
        'method' => 'GET',
        'path' => '/confirm',
        'controller' => [\App\Controller\ConfirmController::class, 'index'],
    ],
    [
        'method' => 'POST',
        'path' => '/confirm/resend',
        'controller' => [\App\Controller\ConfirmController::class, 'resend'],
    ],
    [
        'method' => 'GET',
        'path' => '/confirm/email',
        'controller' => [\App\Controller\ConfirmController::class, 'show'],
    ],
    [
        'method' => 'POST',
        'path' => '/confirm/email',
        'controller' => [\App\Controller\ConfirmController::class, 'confirm'],
    ],
    // ProfileController
    [
        'method' => 'GET',
        'path' => '/profile',
        'controller' => [\App\Controller\ProfileController::class, 'index'],
    ],
    // GalleryController
    [
        'method' => 'GET',
        'path' => '/gallery',
        'controller' => [\App\Controller\GalleryController::class, 'index'],
    ],
    [
        'method' => 'GET',
        'path' => '/gallery/{id}',
        'controller' => [\App\Controller\GalleryController::class, 'show'],
    ],
    [
        'method' => 'DELETE',
        'path' => '/gallery/{id}',
        'controller' => [\App\Controller\GalleryController::class, 'delete'],
    ],
    [
        'method' => 'GET',
        'path' => '/stickers',
        'controller' => [\App\Controller\GalleryController::class, 'getAllStickers'],
    ],
    [
        'method' => 'GET',
        'path' => '/images/{slug}',
        'controller' => [\App\Controller\GalleryController::class, 'getImageData'],
    ],
    [
        'method' => 'POST',
        'path' => '/upload/sticker',
        'controller' => [\App\Controller\GalleryController::class, 'uploadSticker'],
    ],
    [
        'method' => 'POST',
        'path' => '/upload/gallery',
        'controller' => [\App\Controller\GalleryController::class, 'uploadGallery'],
    ],
    // RelationController
    [
        'method' => 'POST',
        'path' => '/relation',
        'controller' => \App\Controller\RelationController::class,
    ],
    // CommentController
    [
        'method' => 'POST',
        'path' => '/comment',
        'controller' => \App\Controller\CommentController::class,
    ],
    // SettingController
    [
        'method' => 'GET',
        'path' => '/setting',
        'controller' => [\App\Controller\SettingController::class, 'index'],
    ],
    [
        'method' => 'GET',
        'path' => '/setting/profile',
        'controller' => [\App\Controller\SettingController::class, 'profile'],
    ],
    [
        'method' => 'GET',
        'path' => '/setting/password',
        'controller' => [\App\Controller\SettingController::class, 'password'],
    ],
    [
        'method' => 'PATCH',
        'path' => '/setting/profile',
        'controller' => [\App\Controller\SettingController::class, 'changeProfile'],
    ],
    [
        'method' => 'PATCH',
        'path' => '/setting/password',
        'controller' => [\App\Controller\SettingController::class, 'changePassword'],
    ],
];