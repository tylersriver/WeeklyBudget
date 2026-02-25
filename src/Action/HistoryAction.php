<?php

declare(strict_types=1);

namespace App\Action;

use App\Repository\TransactionRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class HistoryAction
{
    public function __construct(
        private readonly Twig $view,
        private readonly TransactionRepository $transactions,
    ) {}

    public function index(Request $request, Response $response): Response
    {
        $month = (int) date('n');
        $year  = (int) date('Y');

        return $this->renderHistory($response, $month, $year);
    }

    public function filter(Request $request, Response $response): Response
    {
        $body  = (array) $request->getParsedBody();
        $month = (int) ($body['month'] ?? date('n'));
        $year  = (int) ($body['year']  ?? date('Y'));

        return $this->renderHistory($response, $month, $year);
    }

    private function renderHistory(Response $response, int $month, int $year): Response
    {
        return $this->view->render($response, 'history.html.twig', [
            'transactions'  => $this->transactions->getTransactionsForMonth($year, $month),
            'years'         => $this->transactions->getYearsForTransactions(),
            'selectedMonth' => $month,
            'selectedYear'  => $year,
        ]);
    }
}
