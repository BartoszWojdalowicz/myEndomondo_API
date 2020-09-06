<?php

namespace App\Controller;

use App\Entity\GeneratedUrl;
use App\Entity\Profile;
use App\Entity\User;
use App\Repository\GeneratedUrlRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints;


class RegistrationController extends AbstractFOSRestController
{

    /**
     * @Rest\Post("/api/user", name="app_register")
     * @Rest\QueryParam(name="username")
     * @Rest\QueryParam(name="password")
     * @Rest\QueryParam(name="email", requirements=@Constraints\Email)
     * @param ParamFetcherInterface $fetcher
     *
     */
    public function register( UserPasswordEncoderInterface $passwordEncoder,ParamFetcherInterface $fetcher,SerializerInterface $serializer,
        MailerInterface $mailer ): Response
    {

        $data=$fetcher->all();
        if(!$data){
            return new JsonResponse('',404);
        }

        $user = $serializer->denormalize($data,User::class);
        $user->setPassword(
            $passwordEncoder->encodePassword(
                $user,
                $data['password']
            )
        );

        $profile= new Profile();
        $user->setProfile($profile);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);

            $entityManager->flush();


        $url= new GeneratedUrl();
        $url->setExpiredAt(2);
        $url->setHash(40);
        $url->setType(1);
        $url->setUser($user);

        $entityManager->persist($url);
        $entityManager->flush();

                $confirmEmail=new TemplatedEmail();
                $confirmEmail
                    ->from(new Address('wojdalowicz@op.pl', 'MyEndomondo'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
                    ->context([
                        'url' => '/verify/email/'.$url->getHash(),
                        'user' => $user->getUsername(),
                        'expiredAt'=> $url->getExpiredAt(),
                    ]);
           $mailer->send($confirmEmail);

        return new JsonResponse($user,201);
    }

    /**
     * @Rest\Put("/api/verify/email/{hash}", name="app_verify_email")
     */
    public function verifyUserEmail($hash,GeneratedUrlRepository $generatedUrlRepository): Response
    {
        $url=$generatedUrlRepository->findOneBy(['hash'=>$hash]);

        if($url && $url->isExpired()==true){

            $user=$url->getUser();
            $user->setIsVerified(true);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return new JsonResponse('',200);
        }
        return new JsonResponse('',400);
    }
}
