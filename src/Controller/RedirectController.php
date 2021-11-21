<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Url;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RedirectController extends AbstractController
{
    #[Route('/{shortcode}', name: 'urlRedirect', methods: ['GET'])]
    #[ParamConverter('url', options: ['mapping' => ['shortcode' => 'shortcode']])]
    public function redirectToUrl(Url $urlEntity): Response
    {
        return $this->redirect($urlEntity->getUrl());
    }
}
