<?php

namespace App\Controller;

use App\Entity\NgCity;
use App\Entity\NgState;
use App\Service\Locale\LocaleService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CreateNgCitiesController extends AbstractController
{
    /**
     * @Route("/create/ng/cities", name="create_ng_cities")
     */
    public function index(LocaleService $localeService, EntityManagerInterface $em)
    {
        $ngCities = $localeService->getNgCityChoices();

        foreach ($ngCities as $k => $v) {
        $ngCity = new NgCity();
        $ngCity->setJsonId($v);
        
        $em->persist($ngCity);
        }
        $em->flush();

        return $this->render('create_ng_cities/index.html.twig', [
            'controller_name' => 'CreateNgCitiesController',
        ]);
    }
}
