<?php

namespace App\Controller;

use App\Entity\Continent;
use App\Entity\Country;
use App\Entity\NgState;
use App\Service\Locale\LocaleService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CreateLocalDataController extends AbstractController
{
    /**
     * @Route("/create/local/data", name="create_local_data")
     */
    public function index(LocaleService $localeService, EntityManagerInterface $em)
    {
        $continents = $localeService->getContinentConst();

        foreach ($continents as $k=>$v){
            $continent = new Continent();
            $continent->setCode($k);
            $em->persist($continent);
        }
        $em->flush();

        $countries = $localeService->getCountryChoices();

        foreach($countries as $k=>$v){
            $country = new Country();
            $country->setCode($v);
            $em->persist($country);
        }
        $em->flush();

        $ngStates = $localeService->getNgStateConst();

        foreach($ngStates as $k=>$v){
            $ngState = new NgState();
            $ngState->setJsonId($k);
            $em->persist($ngState);
        }
        $em->flush();


        return $this->render('create_local_data/index.html.twig', [
            'controller_name' => 'CreateLocalDataController',
        ]);
    }
}
