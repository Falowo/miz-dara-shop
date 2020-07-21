<?php

namespace App\Controller;

use App\Entity\Continent;
use App\Entity\Country;
use App\Entity\NgCity;
use App\Entity\NgState;
use App\Service\Locale\LocaleService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class JsonController extends AbstractController
{
    /**
     * @Route("/json5578", name="json")
     */
    public function index(EntityManagerInterface $em)
    {
         

        foreach(LocaleService::getContinentConst() as $k=>$v){
            $continent = new Continent();
            $continent->setCode($k);
            $em->persist($continent);
        }
        $em->flush();

        foreach(LocaleService::getCountryChoices() as $k=>$v){
            $country = new Country();
            $country->setCode($k);
            $em->persist($country);
        }
        $em->flush();

        foreach(LocaleService::getNgStateChoices() as $k=>$v){
            $ngState = new NgState();
            $ngState->setJsonId($v);
            $em->persist($ngState);
        }
        $em->flush();

        foreach(LocaleService::getNgCityChoices() as $k=>$v){
            $ngCity = new NgCity();
            $ngCity->setJsonId($v);
            $em->persist($ngCity);
        }
        $em->flush();
        

        return $this->redirectToRoute('app_index');
    }
}
