<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/catalog', name: 'app_catalog_')]
class CatalogController extends AbstractController
{
    public function __construct(
        private ProductRepository $productRepository
    ) {
    }

    #[Route(path: '/{slug}', name: 'view')]
    public function show(string $slug): Response
    {
        $course = $this->productRepository->findOneBy(['slug' => $slug]);

        if (null === $course) {
            throw $this->createNotFoundException('La page que vous demandez est introuvable.');
        }

        return $this->render('catalog/show.html.twig', [
            'course' => $course,
        ]);
    }

    #[Route(path: '/all', name: 'all', priority: 1)]
    public function all(): Response
    {
        $courses = $this->productRepository->findAll();

        return $this->render('catalog/index.html.twig', [
            'courses' => $courses,
        ]);
    }

    public function similarCourses(int $limit = 2): Response
    {
        $allCourses = $this->productRepository->findAll();
        
        if (empty($allCourses)) {
            $similarCourses = [];
        } else {
            // Pick random elements
            shuffle($allCourses);
            $similarCourses = array_slice($allCourses, 0, min($limit, count($allCourses)));
        }

        return $this->render('catalog/similar_courses.html.twig', [
            'courses' => $similarCourses,
        ]);
    }
}
