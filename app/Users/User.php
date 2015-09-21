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
use DemocracyApps\GB\Organizations\GovernmentOrganization;
use DemocracyApps\GB\Organizations\GovernmentOrganizationUser;
use DemocracyApps\GB\Organizations\MediaOrganization;
use DemocracyApps\GB\Organizations\MediaOrganizationUser;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'email', 'password'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token'];

    public function isVerified ()
    {
        return $this->verified;
    }

    public static function checkVerified($userId)
    {
        $verified = false;
        $user = self::find($userId);
        if ($user != null && $user->verified == true) $verified = true;
        return $verified;
    }

    public function getGovernmentId()
    {
        $id = null;
        $govt = GovernmentOrganizationUser::where('user_id', '=', $this->id)->first();
        if ($govt != null && $govt->access > 0) $id = $govt->government_organization_id;
        return $id;
    }

    public function getGovernmentOrg()
    {
        $org = null;
        $govtuser = GovernmentOrganizationUser::where('user_id', '=', $this->id)->first();
        if ($govtuser != null && $govtuser->access > 0) {
            $org = GovernmentOrganization::find($govtuser->government_organization_id);
        }
        return $org;
    }

    public function getMediaId()
    {
        $id = null;
        $mediauser = MediaOrganizationUser::where('user_id', '=', $this->id)->first();
        if ($mediauser != null && $mediauser->access > 0) $id = $mediauser->media_organization_id;
        return $id;
    }

    public function getMediaOrg()
    {
        $org = null;
        $mediauser = MediaOrganizationUser::where('user_id', '=', $this->id)->first();
        if ($mediauser != null && $mediauser->access > 0) {
            $org = MediaOrganization::find($mediauser->media_organization_id);
        }
        return $org;
    }

}
