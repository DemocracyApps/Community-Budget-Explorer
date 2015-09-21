<?php namespace DemocracyApps\GB\Utility\Mailers;
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

use DemocracyApps\GB\Users\User;
use DemocracyApps\GB\Users\UserConfirmation;

class UserMailer extends Mailer {

    public function confirmEmail (User $user) {

        $confirmation = new UserConfirmation();
        $confirmation->initialize($user, 'em', 24);
        $data = array ('url' => url('auth/confirm'), 'code' => $confirmation->getCode());
        $this->sendTo($user, "Confirm your email at the Government Budget Explorer", 'emails.confirmEmail', $data);
    }

    public function inviteEmail (User $user, $organization) {

        $confirmation = new UserConfirmation();
        $confirmation->initialize($user, 'em', 24);
        $data = array ('url' => url('auth/confirm'), 'code' => $confirmation->getCode(), 'organization' => $organization->name);
        $this->sendTo($user, "Confirm your email at the Government Budget Explorer", 'emails.inviteEmail', $data);
    }

}