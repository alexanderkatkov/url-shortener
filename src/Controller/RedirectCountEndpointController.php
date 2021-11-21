<?php
declare(strict_types=1);

namespace App\Controller;

use App\Repository\UrlRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class RedirectCountEndpointController extends AbstractController
{
    public function __construct(private UrlRepository $repository)
    {
    }

    #[Route('/api/statistics', name: 'urls:total-redirects', methods: ['GET'])]
    public function getRedirectCount(): JsonResponse
    {
        return $this->json($this->repository->countTotalRedirects());
    }
}
