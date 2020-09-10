<?php

namespace App\Security\Voter;

use App\Entity\GpsLog;
use App\Entity\Training;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class GpsLogVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        return in_array($attribute, ['LOG_OWNER'])
            && $subject instanceof GpsLog;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        /** @var GpsLog $gpsLog */
        $gpsLog = $subject;


        switch ($attribute) {
            case 'LOG_OWNER':
               return $this->IsOwner($user,$gpsLog);
        }

        return false;
    }

    private function IsOwner(User $user,GpsLog $gpsLog){


        /** @var Training $training */
        $training=$gpsLog->getTraining();

        if($training->getUser()===$user) {
            return true;
        }

        return false;

    }
}
