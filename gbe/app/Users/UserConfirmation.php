<?php

namespace DemocracyApps\GB\Users;

use Illuminate\Database\Eloquent\Model;

class UserConfirmation extends Model {
    protected $table = 'user_confirmations';

    public function initialize (User $user, $type, $hours) {
        $this->user = $user->id;
        $this->type = $type;
        $this->expires = date('Y-m-d H:i:s', time() + $hours * 60 * 60);
        $this->code = uniqid($type . ".", true);
        $this->done = false;
        $this->save();
    }

    public function getCode () {
        return $this->code;
    }

    public function checkCode($code) {
        $ok = false;
        if (time() < strtotime($this->expires)) {
            if (!$this->done && $code == $this->code) $ok = true;
        }
        return $ok;
    }

    public static function remove($id) {
        \DB::table('user_confirmations')->where('id','=',$id)->delete();
    }
}