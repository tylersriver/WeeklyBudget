<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Action;

use App\Auth\Application\Command\RegisterUserCommand;
use App\Shared\Application\CommandBusInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

final class RegisterAction
{
    public function __construct(
        private readonly Twig $view,
        private readonly CommandBusInterface $commandBus,
    ) {
    }

    public function showForm(Request $request, Response $response): Response
    {
        return $this->view->render($response, 'register.html.twig');
    }

    public function register(Request $request, Response $response): Response
    {
        /** @var array<string, string> $body */
        $body            = (array) $request->getParsedBody();
        $email           = (string) ($body['email'] ?? '');
        $password        = (string) ($body['password'] ?? '');
        $confirmPassword = (string) ($body['confirm_password'] ?? '');

        if ($password !== $confirmPassword) {
            return $this->view->render($response, 'register.html.twig', [
                'error' => 'Passwords do not match.',
                'email' => $email,
            ]);
        }

        try {
            /** @var int $userId */
            $userId = $this->commandBus->dispatch(new RegisterUserCommand(
                email:    $email,
                password: $password,
            ));

            $_SESSION['user_id']    = $userId;
            $_SESSION['user_email'] = strtolower(trim($email));

            $url = RouteContext::fromRequest($request)
                ->getRouteParser()
                ->urlFor('dashboard');

            return $response->withHeader('Location', $url)->withStatus(302);
        } catch (\DomainException $e) {
            return $this->view->render($response, 'register.html.twig', [
                'error' => $e->getMessage(),
                'email' => $email,
            ]);
        }
    }
}
