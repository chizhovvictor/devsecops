<?php

declare(strict_types=1);

namespace App\Controller;

use App\Common\Constant;
use App\Kernel\Attribute\Middleware;
use App\Kernel\Component\Request;
use App\Kernel\Component\Response;
use App\Kernel\Abstract\AbstractController;
use App\Middleware\AuthMiddleware;
use App\Middleware\ConfirmMiddleware;
use App\Middleware\SecurityMiddleware;
use App\Model\User;

class MainController extends AbstractController
{
    public function __construct(
    ) {
    }

    #[Middleware(class: SecurityMiddleware::class)]
    #[Middleware(class: AuthMiddleware::class)]
    public function welcome(Request $request): Response
    {
        $securityUser = $request->request->get('security_user');
        $user = $securityUser
            ? User::find($securityUser)
            : null;

        return $this->render('welcome', [
            'footer' => Constant::FOOTER,
            'header' => Constant::HEADER,
            'username' => $user?->getUsername()
        ]);
    }

    #[Middleware(class: SecurityMiddleware::class)]
    public function about(Request $request): Response
    {
        $securityUser = $request->request->get('security_user');
        $user = $securityUser
            ? User::find($securityUser)
            : null;

        return $this->render('about', [
            'header' => Constant::HEADER,
            'footer' => Constant::FOOTER,
            'username' => $user?->getUsername(),
        ]);
    }

    #[Middleware(class: SecurityMiddleware::class)]
    public function terms(Request $request): Response
    {
        $securityUser = $request->request->get('security_user');
        $user = $securityUser
            ? User::find($securityUser)
            : null;

        return $this->render('terms', [
            'header' => Constant::HEADER,
            'footer' => Constant::FOOTER,
            'username' => $user?->getUsername(),
        ]);
    }
}
