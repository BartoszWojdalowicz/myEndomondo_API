<?php


namespace App\Controller;


use App\Entity\GeneratedUrl;
use App\Repository\GeneratedUrlRepository;
use App\Repository\UserRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractFOSRestController
{
    /**
     * @Rest\Put("/api/password", name="app_change_password")
     * @Rest\QueryParam(name="password1")
     * @Rest\QueryParam(name="password2")
     *
     */
    public function ChangePassword(UserPasswordEncoderInterface $passwordEncoder, ParamFetcherInterface $fetcher){

        $data=$fetcher->all();
        if($data['password1']===$data['password2']) {

            $user = $this->getUser();
            $user->setPassword(
                $passwordEncoder->encodePassword($user, $data['password1'])
            );

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return new Response(null,200);
        }
        return new Response(null,400);
    }

    /**
     * @Rest\Post("/api/password/forgot/request", name="app_forgott_password")
     * @Rest\QueryParam(name="email")
     */
    public function ForgotPassword(ParamFetcherInterface $fetcher,UserRepository $repository,MailerInterface $mailer){

        $data=$fetcher->all();
        $user=$repository->findOneBy(['email'=>$data['email']]);
        if(!$user){
            return new Response(null,404);
        }

        $url= new GeneratedUrl();
        $url->setUser($user);
        $url->setType(2);
        $url->setHash(40);
        $url->setExpiredAt(2);

        $em=$this->getDoctrine()->getManager();
        $em->persist($url);
        $em->flush();

        $confirmEmail=new TemplatedEmail();
        $confirmEmail
            ->from(new Address('wojdalowicz@op.pl', 'MyEndomondo'))
            ->to($user->getEmail())
            ->subject('create new password')
            ->htmlTemplate('password/remind_password.html.twig')
            ->context([
                'url' => '/password/forgot/'.$url->getHash(),
                'user' => $user->getUsername(),
                'expiredAt'=> $url->getExpiredAt(),
            ]);
        $mailer->send($confirmEmail);

        return new Response();

    }

    /**
     * @Rest\Get("/api/password/forgot/{hash}", name="get_forgot_password_hash")
     */
    public function GetForgotPassword(UserRepository $userRepository,$hash,GeneratedUrlRepository $generatedUrlRepository,SerializerInterface $serializer){


        /** @var GeneratedUrl $url */
        $url=$generatedUrlRepository->findOneBy(['hash'=>$hash]);

        if(!$url){
            return new Response(null,404);
        }
        if($url->getEntry() >= 1 || $url->isExpired()==true){
            return new Response(null,400);
        }
        $url->incrementEntry();
        $user=$url->getUser();
        $em=$this->getDoctrine()->getManager();
        $em->flush();

        $user=$serializer->serialize($user,'json',['groups'=>'getForgotPasswordHash']);

        return new Response($user,200);
    }

    /**
     * @Rest\Post("/api/password/forgot", name="post_password_remind")
     * @Rest\RequestParam(name="userId")
     * @Rest\QueryParam(name="password1")
     * @Rest\QueryParam(name="password2")
     */
    public function PostForgotPassword(UserRepository $repository,SerializerInterface $serializer,ParamFetcherInterface $fetcher,UserPasswordEncoderInterface $passwordEncoder){

        $data=$fetcher->all();
        $user=$repository->find($data['userId']);

        if(!$user){
            return new Response(null,404);
        }

        if($data['password1']!==$data['password2']) {
            return new Response(null,400);
        }

        $password=$passwordEncoder->encodePassword($user, $data['password1']);

        if($user->getPassword()==$password){
            return new Response(null,400);
        }

        $user->setPassword($password);

        $em=$this->getDoctrine()->getManager();
        $em->flush();

        return new Response(null,200);

    }
}