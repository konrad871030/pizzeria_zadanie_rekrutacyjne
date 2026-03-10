<?php

namespace App\Controller;

use App\Request\CreateOrderPayload;
use App\Repository\MenuItemRepository;
use App\Service\OrderService;
use App\Service\QueueStatsService;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

final class OrderController
{
    public function __construct(
        private readonly MenuItemRepository $menuItemRepository,
        private readonly OrderService $orderService,
        private readonly QueueStatsService $queueStatsService,
        private readonly Environment $twig,
    ) {
    }

    #[Route('/', name: 'order_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $menuItems = $this->menuItemRepository->findAll();
        $stats = $this->queueStatsService->getStats();
        $flashMessages = $request->getSession()->getFlashBag()->all();

        return new Response($this->twig->render('order/index.html.twig', [
            'menuItems' => $menuItems,
            'stats' => $stats,
            'flashMessages' => $flashMessages,
        ]));
    }

    #[Route('/order', name: 'order_create', methods: ['POST'])]
    public function create(Request $request): RedirectResponse
    {
        try {
            $payload = CreateOrderPayload::fromRequest($request);
            $this->orderService->createOrder(
                $payload->menuItemId,
                $payload->quantity,
                $payload->email,
                $payload->deliveryAddress,
            );
            $request->getSession()->getFlashBag()->add('success', 'Zamówienie zostało przyjęte.');
        } catch (InvalidArgumentException $exception) {
            $request->getSession()->getFlashBag()->add('error', $exception->getMessage());
        }

        return new RedirectResponse('/');
    }
}
