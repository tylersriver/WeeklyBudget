<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Action;

use App\Auth\Application\Command\LoginCommand;
use App\Auth\Application\Command\LoginHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

final class LoginAction
{
    public function __construct(
        private readonly Twig $view,
        private readonly LoginHandler $loginHandler,
    ) {
    }

    public function showForm(Request $request, Response $response): Response
    {
        return $this->view->render($response, 'login.html.twig');
    }

    public function login(Request $request, Response $response): Response
    {
        /** @var array<string, string> $body */
        $body     = (array) $request->getParsedBody();
        $email    = (string) ($body['email'] ?? '');
        $password = (string) ($body['password'] ?? '');

        try {
            $user = ($this->loginHandler)(new LoginCommand(
                email:    $email,
                password: $password,
            ));

            $_SESSION['user_id']    = $user->getId();
            $_SESSION['user_email'] = $user->getEmail()->toString();

            $url = RouteContext::fromRequest($request)
                ->getRouteParser()
                ->urlFor('dashboard');

            return $response->withHeader('Location', $url)->withStatus(302);
        } catch (\DomainException $e) {
            return $this->view->render($response, 'login.html.twig', [
                'error' => $e->getMessage(),
                'email' => $email,
            ]);
        }
    }
}
