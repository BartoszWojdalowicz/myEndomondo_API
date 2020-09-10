<?php

namespace App\Controller;

use App\Entity\GpsLog;
use App\Entity\Training;
use App\Repository\GpsLogRepository;
use App\Repository\TrainingRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class GpsLogsController extends AbstractFOSRestController
{
    /**
     * @Rest\Post("/api/log", name="post_log")
     * @Rest\RequestParam(name="training")
     * @Rest\RequestParam(name="longitude")
     * @Rest\RequestParam(name="latitude")
     * @Rest\RequestParam(name="speed")
     * @Rest\RequestParam(name="height")
     * @Rest\RequestParam(name="createdAt")
     * @Rest\RequestParam(name="isStop")
     * @Rest\RequestParam(name="isPaused")
     */
    public function PostGpsLog(ParamFetcherInterface $fetcher,TrainingRepository $trainingRepository,SerializerInterface $serializer){


        $data=$fetcher->all();
        $training=$trainingRepository->find($data['training']);
        $this->denyAccessUnlessGranted('TRAINING_OWNER', $training);

        $data['training']=$training;

        $gpsLog = $serializer->denormalize($data, GpsLog::class);
        $gpsLog->setTraining($training);

        $em=$this->getDoctrine()->getManager();
        $em->persist($gpsLog);
        $em->flush();

        $gpsLogs = $serializer->serialize($gpsLog, 'json', ['groups' => 'post_gps_log']);


        return new Response($gpsLogs,201);
    }


    /**
     * @Rest\Get("/api/logs/{training}", name="get_logs")
     * @IsGranted("TRAINING_OWNER", subject="training")
     */
    public function GetGpsLogs(Training $training,GpsLogRepository $gpsLogRepository,SerializerInterface $serializer){

        $gpsLogs=$gpsLogRepository->findBy(['training'=>$training]);
        $gpsLogs = $serializer->serialize($gpsLogs, 'json', ['groups' => 'get_gps_log']);
        return new Response($gpsLogs, 200);

    }

    /**
     * @Rest\Get("/api/log/{gpsLog}", name="get_log")
     * @IsGranted("LOG_OWNER", subject="gpsLog")
     */
    public function GetGpsLog(GpsLog $gpsLog,SerializerInterface $serializer){

        $gpsLog = $serializer->serialize($gpsLog, 'json', ['groups' => 'get_gps_log']);
        return new Response($gpsLog, 200);

    }



}