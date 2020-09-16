<?php


namespace App\Controller;


use App\Entity\Training;
use App\Entity\TrainingType;
use App\Entity\User;
use App\Repository\GpsLogRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class StatisticController extends AbstractFOSRestController
{
    /**
     * @Rest\Get("/api/{training}/statistic", name="get_statistic")
     */
    public function GetStatistic(Training $training,SerializerInterface $serializer){

        $statistic=$training->getStatistic();
        $statistic = $serializer->serialize($statistic, 'json', ['groups' => 'get_statistic']);

        return new Response($statistic,200);

    }

    /**
     * @Rest\Put("/api/{training}/statistics", name="post_statistic")
     */
    public function UpdateStatistic(GpsLogRepository $gpsLogRepository,Training $training){

        $gpsLogs=$gpsLogRepository->findBy(['training'=>$training],['createdAt'=>'ASC']);
        $results=$this->CalculateDistanceStatistic($gpsLogs);

        $statistic=$training->getStatistic();
        $statistic->setAvgSpeed($results['avgSpeed']);
        $statistic->setMaxSpeed($results['maxSpeed']);
        $statistic->setMaxHeight($results['maxHeight']);
        $statistic->setMinSpeed($results['minSpeed']);
        $statistic->setMinHeight($results['minHeight']);
        $statistic->setNumberOfBreaks($results['breaks']);
        $statistic->setDuration($results['duration']);
        $statistic->setKcal($this->CalculateKcal($this->getUser(),$training,$results['duration']));

        $results['kcal']=$statistic->getKcal();

        $em=$this->getDoctrine()->getManager();
        $em->persist($statistic);
        $em->flush();

        return new JsonResponse($results,200);
    }


    /**
     *
     * @Rest\Post("/api/training/type", name="post_training_type")
     * @Rest\RequestParam(name="Name")
     * @Rest\RequestParam(name="menMultipler")
     * @Rest\RequestParam(name="womenMultipler")
     */
    public function PostTrainingType(ParamFetcherInterface $fetcher,SerializerInterface $serializer){

        $data=$fetcher->all();
        $trainingType = $serializer->denormalize($data, TrainingType::class);

        $em=$this->getDoctrine()->getManager();
        $em->persist($trainingType);
        $em->flush();

        return new JsonResponse(null,200);
    }


    public function DeleteStatistic(){

    }

    private function CalculateDistanceStatistic(array $gpsLogs){

        $results=['minSpeed'=>0,
                    'maxSpeed'=>0,
                    'minHeight'=>0,
                    'avgSpeed'=>0,
                    'maxHeight'=>0,
                    'breaks'=>0,
                    'totalDistance'=>0,
                    'timeStart'=>0,
                    'timeEnd'=>0,
                    'duration'=>0,];

        $temporaryData=['temporaryDistance'=>0,
                        'temporaryPartsOfTripDistance'=>0,];

        $count=count($gpsLogs);

        for ($i = 0; $i <= $count; $i++) {

            $speed= $gpsLogs[$i]->getSpeed();
            $width= $gpsLogs[$i]->getHeight();
            $lat1 = $gpsLogs[$i]->getLatitude();
            $lat2 = $gpsLogs[$i+1]->getLatitude();
            $lon1 = $gpsLogs[$i]->getLongitude();
            $lon2 = $gpsLogs[$i+1]->getLongitude();
            $results['avgSpeed']+=$speed;

            if ($i==0){
                $results['minSpeed']=$speed;
                $results['maxSpeed']=$speed;
                $results['minHeight']=$width;
                $results['maxHeight']=$width;
                $results['timeStart']=$gpsLogs[$i]->getCreatedAt();
                $results['partTimeStart']=$gpsLogs[$i]->getCreatedAt();
            }

            if($speed<$results['minSpeed'])
                $results['minSpeed']=$speed;
            if($speed>$results['maxSpeed'])
                $results['maxSpeed']=$speed;

            if($width<$results['minHeight'])
                $results['minHeight']=$width;
            if($width>$results['maxHeight'])
                $results['maxHeight']=$width;

            $distance=$this->CalculateDistanceBetween2GpsLogs($lat1,  $lat2,$lon1, $lon2);

            $results['totalDistance'] += $distance;
            $temporaryData['temporaryDistance'] = $distance;

            if($gpsLogs[$i+1]->getIsPaused()==true){

                $speed1= $gpsLogs[$i+1]->getSpeed();
                $width1= $gpsLogs[$i+1]->getHeight();
                $results['partTimeEnd']=$gpsLogs[$i+1]->getCreatedAt();

                if($speed1<$results['minSpeed'])
                    $results['minSpeed']=$speed1;
                if($speed1>$results['maxSpeed'])
                    $results['maxSpeed']=$speed1;

                if($width1<$results['minHeight'])
                    $results['minHeight']=$width1;
                if($width1>$results['maxHeight'])
                    $results['maxHeight']=$width1;
                $totalTime= (date_diff($results['partTimeEnd'],$results['partTimeStart']));

                $partOfTrip=['partDistance'=>$results['totalDistance']-$temporaryData['temporaryPartsOfTripDistance'],
                            'maxSpeed'=>$results['maxSpeed'],
                            'minSpeed'=>$results['minSpeed'],
                            'maxWidth'=>$results['maxHeight'],
                            'minWidth'=>$results['minHeight'],
                            'avgSpeed'=>($results['avgSpeed']+$speed1)/($i+1),
                            'partTime'=>$totalTime->format('%H:%I:%S')];

                $temporaryData['temporaryPartsOfTripDistance']=$partOfTrip['partDistance'];
                $results['breaks']+=1;
                $results['partOfTrip'][]=$partOfTrip;
                $i++;
            }

            if($gpsLogs[$i+1]->getIsStop()==true){

                $speed1= $gpsLogs[$i+1]->getSpeed();
                $width1= $gpsLogs[$i+1]->getHeight();

                if($speed1<$results['minSpeed'])
                    $results['minSpeed']=$speed1;
                if($speed1>$results['maxSpeed'])
                    $results['maxSpeed']=$speed1;

                if($width1<$results['minHeight'])
                    $results['minHeight']=$width1;
                if($width1>$results['maxHeight'])
                    $results['maxHeight']=$width1;

                $results['timeEnd']=$gpsLogs[$i+1]->getCreatedAt();
                $results['avgSpeed']= ($results['avgSpeed']+$speed1)/($i+1);
                $totalTime= (date_diff($results['timeEnd'],$results['timeStart']));
                $results['duration']=$totalTime->format('%H:%I:%S');
                return $results;
            }
        }
    }

    private function CalculateDistanceBetween2GpsLogs(float $lat1, float $lat2,float $lon1,float $lon2){

        $diffLat = deg2rad($lat2 - $lat1);
        $diffLon = deg2rad($lon2 - $lon1);
        $R = 6371000;

        $a = sin($diffLat/2) * sin($diffLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($diffLon/2) * sin($diffLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $R * $c;

        return $distance;
    }

    private function CalculateKcal(User $user,Training $training,string $duration){

        $trainingType=$training->getType();
        $statistic=$training->getStatistic();
        $profile=$user->getProfile();
        $duration=explode(':',$duration);
        $durationInSec=(($duration[0]*3600)+($duration[1]*60)+$duration[2]);

        $sexMultipler=$profile->getSex()=='1'? $trainingType->getMenMultipler() : $trainingType->getWomenMultipler();
        $kcal=($profile->getWeight()* $sexMultipler * $durationInSec)/100;

        return $kcal;

    }

}