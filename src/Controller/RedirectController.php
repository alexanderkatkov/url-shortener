<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Url;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RedirectController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    #[Route('/{shortcode}', name: 'urlRedirect', methods: ['GET'])]
    #[ParamConverter('url', options: ['mapping' => ['shortcode' => 'shortcode']])]
    public function redirectToUrl(Url $urlEntity): Response
    {
        $urlEntity->increaseRedirectCount();

        $this->entityManager->persist($urlEntity);
        $this->entityManager->flush();

        return $this->redirect($urlEntity->getUrl());
    }
}
