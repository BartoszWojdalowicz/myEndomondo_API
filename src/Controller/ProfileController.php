<?php


namespace App\Controller;


use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class ProfileController extends AbstractFOSRestController
{
    /**
     * @Rest\Get("/api/profile", name="app_get_profile")
     */
    public function GetProfile(SerializerInterface $serializer){

        $user=$this->getUser();
        if(!$user){
            return new Response('',404);
        }

        $profile=$user->getProfile();
        $profile=$serializer->serialize($profile,'json');

        return new Response($profile,200);
    }

    /**
     * @Rest\Put("/api/profile", name="app_put_profile")
     * @Rest\QueryParam(name="name")
     * @Rest\QueryParam(name="surname")
     * @Rest\QueryParam(name="city")
     * @Rest\QueryParam(name="country")
     * @Rest\QueryParam(name="weight")
     * @Rest\QueryParam(name="sex")
     * @Rest\QueryParam(name="growth")
     * @Rest\QueryParam(name="isPublic")
     */
    public function PutProfile(ParamFetcherInterface $fetcher,SerializerInterface $serializer){

        $user=$this->getUser();
        if(!$user){
            return new Response('',404);
        }

        $profile=$user->getProfile();
        $data=$fetcher->all();

        extract($data); //ekstrachuje pola tablicy jako zmienne

        if($name)
            $profile->setName($name);
        if($surname)
            $profile->setSurname($surname);
        if($city)
            $profile->setCity($city);
        if($country)
            $profile->setCountry($country);
        if($weight)
            $profile->setWeight($weight);
        if($sex)
            $profile->setSex($sex);
        if($growth)
            $profile->setGrowth($growth);
        if($isPublic)
            $profile->setIsPublic($isPublic);

        $profile=$serializer->serialize($profile,'json');

        $em=$this->getDoctrine()->getManager();
        $em->flush();

        return new Response($profile,200);
    }

    /**
     * @Rest\Delete("/api/profile", name="app_delete_profile")
     */
    public function DeleteProfile(){
        //TODO : usuwanie wszystkich kluczy obcych itd.
    }

    private function DeleteUser(){
        //TODO : usuwanie wszystkich kluczy obcych itd.

    }




}