<?php namespace DemocracyApps\GB\Utility\Mailers;


use DemocracyApps\GB\Users\User;
use DemocracyApps\GB\Users\UserConfirmation;

class UserMailer extends Mailer {

    public function confirmEmail (User $user) {

        $confirmation = new UserConfirmation();
        $confirmation->initialize($user, 'em', 24);
        $data = array ('url' => url('auth/confirm'), 'code' => $confirmation->getCode());
        $this->sendTo($user, "Confirm your email at Community Narratives Platform", 'emails.confirmEmail', $data);
    }

}