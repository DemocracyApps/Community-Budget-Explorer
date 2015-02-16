<?php


namespace DemocracyApps\GB\Utility\Mailers;


abstract class Mailer {

    public function sendTo($user, $subject, $view, $data = [])
    {
        \Mail::queue($view, $data, function ($message) use($user, $subject) {
            $message->to($user->email) -> subject ($subject);
        });
    }

}