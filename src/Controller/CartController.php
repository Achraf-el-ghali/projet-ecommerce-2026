<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\Cart\CartHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/cart', name: 'app_cart_')]
class CartController extends AbstractController
{
    public function __construct(
        private CartHandler $cartHandler
    ) {
    }

    #[Route(path: '/', name: 'index')]
    public function index(): Response
    {
        return $this->render('cart/index.html.twig', [
            'items' => $this->cartHandler->getCartItems(),
            'total' => $this->cartHandler->getCartTotal(),
        ]);
    }

    #[Route(path: '/add/{id}', name: 'add')]
    public function add(int $id, Request $request): Response
    {
        try {
            $this->cartHandler->addToCart($id);
            $this->addFlash('success', 'La formation a été ajoutée avec succès à votre panier.');
        } catch (\InvalidArgumentException $e) {
            $this->addFlash('danger', $e->getMessage());
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Une erreur inattendue est survenue.');
        }

        // Redirect to referer if available, or fallback to catalog all
        $referer = $request->headers->get('referer');
        if ($referer) {
            return $this->redirect($referer);
        }

        return $this->redirectToRoute('app_catalog_all');
    }

    #[Route(path: '/remove/{id}', name: 'remove')]
    public function remove(int $id): Response
    {
        $this->cartHandler->removeFromCart($id);
        $this->addFlash('warning', 'La formation a été retirée de votre panier.');

        return $this->redirectToRoute('app_cart_index');
    }

    #[Route(path: '/clear', name: 'clear')]
    public function clear(): Response
    {
        $this->cartHandler->clearCart();
        $this->addFlash('info', 'Votre panier a été vidé.');

        return $this->redirectToRoute('app_cart_index');
    }
}
