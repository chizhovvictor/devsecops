<?php

declare(strict_types=1);

namespace App\Controller;

use App\Common\Constant;
use App\Kernel\Abstract\AbstractController;
use App\Kernel\Attribute\Middleware;
use App\Kernel\Component\Request;
use App\Kernel\Component\Response;
use App\Middleware\AuthMiddleware;
use App\Middleware\ConfirmMiddleware;
use App\Middleware\SecurityMiddleware;
use App\Model\User;

class ProfileController extends AbstractController
{
    #[Middleware(class: SecurityMiddleware::class)]
    #[Middleware(class: AuthMiddleware::class)]
    public function index(Request $request): Response
    {
        $securityUser = $request->request->get('security_user');
        $user = User::find($securityUser);

        return $this->render('profile', [
            'header' => Constant::HEADER,
            'footer' => Constant::FOOTER,
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'user_id' => $user->getId(),
        ]);
    }
}
