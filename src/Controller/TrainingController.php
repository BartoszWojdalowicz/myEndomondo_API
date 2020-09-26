<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Statistic;
use App\Entity\Training;
use App\Repository\GpsLogRepository;
use App\Repository\ImageRepository;
use App\Repository\TrainingRepository;
use App\Services\ImageUploader;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class TrainingController extends AbstractFOSRestController
{
    /**
     * @Rest\Post("/api/training", name="post_training")
     * @Rest\QueryParam(name="name")
     * @Rest\QueryParam(name="notes")
     * @Rest\QueryParam(name="description")
     */
    public function PostTraining(SerializerInterface $serializer, ParamFetcherInterface $fetcher)
    {
        $user = $this->getUser();
        if (!$user) {
            return new Response(null, 404);
        }

        $data = $fetcher->all();

        $training = $serializer->denormalize($data, Training::class);
        $training->setUser($user);

        $statistic= new Statistic();
        $training->setStatistic($statistic);
        $em = $this->getDoctrine()->getManager();
        $em->persist($statistic);
        $em->persist($training);
        $em->flush();

        $training = $serializer->serialize($training, 'json', ['groups' => 'post_training']);

        return new Response($training, 201);
    }

    /**
     * @Rest\Get("/api/training/{training}", name="get_traning")
     * @IsGranted("TRAINING_OWNER", subject="training")
     */
    public function GetTraining(Training $training, SerializerInterface $serializer)
    {
        $training = $serializer->serialize($training, 'json', ['groups' => 'get_training']);
        return new Response($training, 200);
    }

    /**
     * @Rest\Get("/api/trainings", name="get_trainings")
     * @Rest\QueryParam(name="limit",default="10")
     * @Rest\QueryParam(name="offset",default="0")
     */
    public function GetTrainings(TrainingRepository $repository, SerializerInterface $serializer,ParamFetcherInterface $fetcher)
    {
        $user = $this->getUser();

        $PaginationCount=$repository->CountAllUsersTraning($user);
        $limit=$fetcher->get('limit');
        $offset=$fetcher->get('offset');
        $trainings = $repository->findBy(['user' => $user],[],$limit,$offset);
        $trainings = $serializer->serialize($trainings, 'json', ['groups' => 'get_training']);

        return new Response($trainings,200,['X-Pagination-Count'=>$PaginationCount]);
    }

    /**
     * @Rest\Put("/api/training/{training}", name="put_training")
     * @Rest\QueryParam(name="name")
     * @Rest\QueryParam(name="notes")
     * @Rest\QueryParam(name="description")
     * @IsGranted("TRAINING_OWNER", subject="training")
     */
    public function PutTraining(Training $training, SerializerInterface $serializer,ParamFetcherInterface $fetcher)
    {
        $data = $fetcher->all();

        extract($data); //ekstrachuje pola tablicy jako zmienne

        if ($name) {
            $training->setName($name);
        }
        if ($description) {
            $training->setDescription($description);
        }
        if ($notes) {
            $training->setNotes($notes);
        }

        $training = $serializer->serialize($training, 'json',['groups' => 'get_training']);

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return new Response($training, 200);
    }


    /**
     * @Rest\Delete("/api/training/{training}", name="delete_training")
     * @IsGranted("TRAINING_OWNER", subject="training")
     */
    public function DeleteTraining (Training $training){

        $em=$this->getDoctrine()->getManager();
        $em->remove($training);
        $em->flush();

        return new Response(null, 200);

    }

    /**
     * @Rest\Post("/api/{training}/image", name="post_training_image")
     * @Rest\FileParam(name="image")
     * @Rest\RequestParam(name="name")
     * @Rest\RequestParam(name="isMain")
     */
    public function postTravelImage(Training $training,ImageUploader $imageUploader,ImageRepository $imageRepository,ParamFetcherInterface $fetcher,GpsLogRepository $gpsLogRepository){

        $data=$fetcher->all();

        $failed=$imageUploader->CheckImagesRequirments($data['image']);
        if ($failed==false){
            return new Response('image must be in jpg,jpeg or png format, 1000x1000 resolution and 2 mb max size',400);
        }

        $em=$this->getDoctrine()->getManager();
        $guessExtension = $data['image']->guessExtension();
        $image=$imageUploader->UploadImage($data['image']);
        if ($guessExtension == 'jpeg' || $guessExtension == 'jpg') {
                $metadata = exif_read_data($this->getParameter('images_directory')."/".$image->getCreatedAt()->format("Y/m".'/'.$image->getName()));
            }
            if (isset($metadata['DateTimeOriginal'])) {          //automatyczne wyszukiwanie najblizszego loga na podstawie daty oraz id podrózy. jeśli nie ma to ustaw null
                $result = $gpsLogRepository->getNearestGPSLog($training, $image->getId()->getDateTime());
                if(!empty($result)){
                    $image->setGpsLog($result[0]);
            }
            $trainingMainImage = $imageRepository->findOneBy(['training'=>$training,'isMain'=>true]);
            if(isset($trainingMainImage) && $image->getIsMain()==true){
                $trainingMainImage->setIsMain(false);
                $em->persist($trainingMainImage);
            }
            $image->setTraining($training);
            $image->setIsPublic(false);
            $image->setIsMain($data['isMain']);
            $em->persist($image);
            $em->flush();
            }

        return new Response(null,201);
    }

    /**
     * @Rest\Get("/api/{training}/images")
     */
    public function getTrainingImage(Training $training,ImageRepository $repository,SerializerInterface $serializer)
    {
        $images=$repository->findBy(['training'=>$training,]);
        if(!$images){
            return new Response(null,204);
        }
        $images=$serializer->serialize($images, 'json',['groups' => '']);

        return new Response($images,200);
    }

    /**
     * @Rest\PUT("/api/{training}/{image}")
     */
    public function putTravelImage(Training $training,Image $image,ParamFetcherInterface $fetcher,ImageUploader $imageUploader,ImageRepository $imageRepository,GpsLogRepository $gpsLogRepository){

        if ($training->getId() !== $image->getTraining()->getId()) {
            return new Response ("you try edit image from other travel.", Response::HTTP_UNAUTHORIZED);
        }
        //jeżeli będzie opcja podmiany zdjęcia to musi być wysyłane metodą POST z parametrem : _method=PUT
        $newImage=$fetcher->all();
        if(isset($newImage) && !empty($newImage['image'])){

            $failed=$imageUploader->CheckImagesRequirments($newImage['image']);
            if($failed==false){
                return new Response('image must be in jpg,jpeg or png format, 6000x4000 resolution and 6 mb max size',400);
            }
            $newImage=$imageUploader->UploadImage($newImage); //jesli new image jest poprawny
            unlink($this->getParameter('images_directory')."/".($image->getCreatedAt()->format("Y/m").'/'.$image->getName()));

            $image->setName($newImage->getName());
        }

        $isPublic=$newImage['isPublic'];
        $isMain=$newImage['isMain'];
        if (isset($isPublic)){
            $image->setIsPublic($isPublic);
        }
        if (isset($isMain)){
            if($isMain==false || $isMain==0){
                $image->setIsMain($isMain);
            }
            $mainImage= $imageRepository->findOneBy(['training'=>$training,'isMain'=>true]);
            if(isset($imageWithCover) && ($isMain==true)){
                $imageWithCover->setIsMain(false);
                $image->setIsMain($isMain);
            }
            elseif(!isset($mainImage) && $isMain==true ) {
                $image->setIsMain($isMain);
            }
        }

        if($newImage['gpsLog']){
            $gpsLog=$gpsLogRepository->findBy(['training'=>$training,'id'=>$newImage['gpsLog']]);
            if(!is_null($gpsLog)){
                $image->setGpsLog($gpsLog[0]);
            }
        };
        $em=$this->getDoctrine()->getManager();
        $em->persist($image);
        $em->flush();

        return new Response(null,200);
    }

    /**
     * @Rest\Delete("/api/{training}/{image}")
     */
    public function deleteTravelImage(Image $image,Training $training){

        if ($training->getId() !== $image->getTraining()->getId()) {
            return new Response ("you try remove image from other travel.", Response::HTTP_UNAUTHORIZED);
        }

        unlink($this->getParameter('images_directory')."/".($image->getCreatedAt()->format("Y/m").'/'.$image->getName()));
        $entityManager=$this->getDoctrine()->getManager();
        $entityManager->remove($image);
        $entityManager->flush();
        return new Response(null,Response::HTTP_OK);
    }
}