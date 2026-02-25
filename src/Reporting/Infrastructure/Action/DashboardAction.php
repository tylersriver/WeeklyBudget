<?php

declare(strict_types=1);

namespace App\Reporting\Infrastructure\Action;

use App\Reporting\Application\Query\GetDashboardQuery;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class DashboardAction
{
    public function __construct(
        private readonly Twig $view,
        private readonly GetDashboardQuery $getDashboard,
    ) {}

    public function __invoke(Request $request, Response $response): Response
    {
        $data = ($this->getDashboard)();

        return $this->view->render($response, 'dashboard.html.twig', $data->toTemplateVars());
    }
}
