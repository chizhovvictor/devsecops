<?php

namespace App\Controller;

use App\Kernel\Attribute\Middleware;
use App\Kernel\Component\JsonResponse;
use App\Kernel\Component\Request;
use App\Kernel\Abstract\AbstractController;
use App\Kernel\Component\Response;
use App\Kernel\Exception\BadRequestHttpException;
use App\Middleware\CsrfMiddleware;
use App\Middleware\RedirectToMain;
use App\Model\Session;
use App\Model\User;
use App\Service\SecurityService;
use App\Service\ValidateService;
use JsonException;
use App\Common\Constant;

class LoginController extends AbstractController
{
    public function __construct(
        private readonly SecurityService $securityService,
        private readonly ValidateService $validateService,
    ) {
    }

    #[Middleware(class: RedirectToMain::class)]
    public function index(): Response
    {
        return $this->render('login', [
            'footer' => Constant::FOOTER,
        ]);
    }

    #[Middleware(class: CsrfMiddleware::class)]
    public function login(Request $request): JsonResponse
    {
        if ($request->getContentType() !== 'application/json') {
            throw new BadRequestHttpException('Content-Type must be application/json');
        }

        try {
            [
                'email' => $email,
                'password' => $password,
            ] = json_decode($request->getContent(), true, 8, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        try {
            $this->validateService->email($email);
            $this->validateService->password($password);
        } catch (\Throwable $exception) {
            return $this->json(['message' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        $user = User::findOneBy(['email:eq' => $email]);
        if (!$user) {
            $message = 'User not found.';
            return $this->json(['message' => $message], Response::HTTP_NOT_FOUND,);
        }

        if (!password_verify($password, $user->getPassword())) {
            $message = 'Wrong password.';
            return $this->json(['message' => $message], Response::HTTP_FORBIDDEN,);
        }

        $token = $this->securityService->generateToken($user);

        return $this->json($token, Response::HTTP_OK);
    }

    #[Middleware(class: CsrfMiddleware::class)]
    public function refreshToken(Request $request): JsonResponse
    {
        if ($request->getContentType() !== 'application/json') {
            throw new BadRequestHttpException('Content-Type must be application/json');
        }

        try {
            [
                'user_id' => $userId,
                'refresh_token' => $refreshToken,
            ] = json_decode($request->getContent(), true, 8, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new BadRequestHttpException('Unable to decode refresh token request.');
        }

        $user = User::find((int)$userId);
        if (!$user) {
            $message = 'User not found.';
            return $this->json(['message' => $message], Response::HTTP_BAD_REQUEST);
        }

        $latestSession = Session::findOneBy(
            ['user_id:eq' => $user->getId()],
            ['id' => 'DESC']
        );

        if (
            !$latestSession ||
            !$latestSession->getRefreshToken() ||
            $latestSession->getRefreshToken() !== $refreshToken ||
            (new \DateTime())->modify('-1 month') > $latestSession->getCreatedAt()
        ) {
            $message = 'Session not found.';
            return $this->json(['message' => $message], Response::HTTP_BAD_REQUEST);
        }

        $token = $this->securityService->generateToken($user);

        return $this->json($token, Response::HTTP_OK);
    }
}
