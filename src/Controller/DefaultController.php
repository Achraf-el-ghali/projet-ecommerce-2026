<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DefaultController extends AbstractController
{
    #[Route('/default', name: 'app_default')]
    public function index(Request $requestObject): Response
    {
        // récupérer GET params men URL
        $page = $requestObject->query->getInt('page', 1);
        $limit = $requestObject->query->getInt('limit', 10);

        // récupérer POST params men formulaire / API
        $postData = $requestObject->request->all();

        // récupérer info serveur (IP)
        $clientIp = $requestObject->server->get('REMOTE_ADDR');

        dump($page, $limit, $postData, $clientIp);

        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }
}

/*
    #[Route('/default', name: 'app_default')]
    public function index(Request $requestObject): Response
    {
        // Récupérer GET params
        $page = $requestObject->query->getInt('page', 1);
        $limit = $requestObject->query->getInt('limit', 5);

        // Récupérer POST params (futur si tu envoies un form)
        $username = $requestObject->request->get('username', 'Guest');

        // Afficher dans Profiler
        dump($page, $limit, $username);

        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'page' => $page,
            'limit' => $limit,
            'username' => $username
        ]);
    }
*/

