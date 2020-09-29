<?php
namespace App\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshTokenRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

class AuthenticationSuccessListener
{
//    private $entityManager;
//    /**
//     * @var RefreshToken
//     */
//    private $refreshToken;
//
//    public function __construct(EntityManagerInterface $entityManager,RefreshTokenRepository $refreshToken)
//    {
//        $this->entityManager = $entityManager;
//        $this->refreshToken = $refreshToken;
//    }

//    /**
//     * @param AuthenticationSuccessEvent $event
//     */
//    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
//    {
//        $data = $event->getData();
//        $user = $event->getUser();
//dd($data);
//        $this->refreshToken->find();
//
//        if (!$user instanceof UserInterface) {
//            return;
//        }
//
//        $data['data'] = array(
//            'roles' => $user->getRoles(),
//        );
//
//        $event->setData($data);
//    }

}