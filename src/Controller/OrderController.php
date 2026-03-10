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
    /**
     * @var array<string, array{ingredients: string, image: string}>
     */
    private const MENU_VIEW_DATA = [
        'Margherita' => [
            'ingredients' => 'Sos pomidorowy, mozzarella, bazylia',
            'image' => '/images/menu/margherita.png',
        ],
        'Capricciosa' => [
            'ingredients' => 'Sos pomidorowy, mozzarella, szynka, pieczarki',
            'image' => '/images/menu/capricciosa.png',
        ],
        'Pepperoni' => [
            'ingredients' => 'Sos pomidorowy, mozzarella, pepperoni',
            'image' => '/images/menu/pepperoni.png',
        ],
        'Diavola' => [
            'ingredients' => 'Sos pomidorowy, mozzarella, salami piccante, chili',
            'image' => '/images/menu/diavola.png',
        ],
        'Funghi' => [
            'ingredients' => 'Sos pomidorowy, mozzarella, pieczarki',
            'image' => '/images/menu/funghi.png',
        ],
        'Quattro Formaggi' => [
            'ingredients' => 'Mozzarella, gorgonzola, parmezan, provolone',
            'image' => '/images/menu/quattro_formaggi.png',
        ],
        'Hawajska' => [
            'ingredients' => 'Sos pomidorowy, mozzarella, szynka, ananas',
            'image' => '/images/menu/hawajska.png',
        ],
        'Wiejska' => [
            'ingredients' => 'Sos pomidorowy, mozzarella, kiełbasa, cebula, ogorek',
            'image' => '/images/menu/wiejska.png',
        ],
    ];

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
        $menuCards = [];

        foreach ($menuItems as $item) {
            $viewData = self::MENU_VIEW_DATA[$item->name] ?? [
                'ingredients' => 'Lista skladnikow do uzupelnienia',
                'image' => '/images/menu/placeholder.jpg',
            ];
            $menuCards[] = [
                'item' => $item,
                'ingredients' => $viewData['ingredients'],
                'image' => $viewData['image'],
            ];
        }

        return new Response($this->twig->render('order/index.html.twig', [
            'menuItems' => $menuItems,
            'menuCards' => $menuCards,
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
