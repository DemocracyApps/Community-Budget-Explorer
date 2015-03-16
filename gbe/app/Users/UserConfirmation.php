<?php namespace DemocracyApps\GB\Users;
/**
 *
 * This file is part of the Government Budget Explorer (GBE).
 *
 *  The GBE is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GBE is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with the GBE.  If not, see <http://www.gnu.org/licenses/>.
 */
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