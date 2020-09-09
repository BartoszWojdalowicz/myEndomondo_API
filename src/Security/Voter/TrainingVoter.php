<?php

namespace App\Security\Voter;

use App\Entity\Training;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class TrainingVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        return in_array($attribute, ['TRAINING_OWNER'])
            && $subject instanceof Training;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        /** @var Training $training */
        $training = $subject;


        switch ($attribute) {
            case 'TRAINING_OWNER':
               return $this->IsOwner($user,$training);
        }

        return false;
    }

    private function IsOwner(User $user,Training $training){

        if($training->getUser()===$user) {
            return true;
        }
        return false;

    }
}
