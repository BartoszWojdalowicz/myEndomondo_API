<?php


namespace App\Controller;

use App\Entity\Statistic;
use App\Entity\Training;
use App\Repository\TrainingRepository;
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

        /** @var Training $training */
        $training = new Training();
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

}