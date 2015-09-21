<?php namespace DemocracyApps\GB\Http\Controllers\Media;

use DemocracyApps\GB\Http\Controllers\Controller;

use DemocracyApps\GB\Organizations\MediaOrganization;
use DemocracyApps\GB\Organizations\MediaOrganizationUser;
use DemocracyApps\GB\Users\User;
use DemocracyApps\GB\Utility\Mailers\UserMailer;
use Illuminate\Http\Request;

class MediaUsersController extends Controller {

    protected $mediaOrganization = null;

    public function __construct(MediaOrganization $org)
    {
        $this->mediaOrganization = $org;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($media_org_id)
    {
        $organization = MediaOrganization::find($media_org_id);
        $users = MediaOrganizationUser::where('media_organization_id', '=', $media_org_id)->get();
        $userMap = array();
        $accessMap = array(
            '0'=>'No privileges',
            '1'=>'Unknown',
            '2'=>'Unknown',
            '3'=>'Unknown',
            '4'=>'Unknown',
            '5'=>'Unknown',
            '6'=>'Unknown',
            '7'=>'Unknown',
            '8'=>'Unknown',
            '9'=>'Administrator',
        );
        foreach ($users as $user) {
            $u = User::find($user->user_id);
            $userMap[$u->id] = $u;
        }

        return view('media.users.index', array('organization'=>$organization, 'users'=>$users,
            'userMap'=>$userMap, 'accessMap'=>$accessMap));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create($media_org_id, Request $request)
    {
        $organization = MediaOrganization::find($media_org_id);
        return view('media.users.create', array('organization'=>$organization));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store($media_org_id, Request $request)
    {
        $rules = ['email'=>'required | email', 'access'=>'required'];
        $this->validate($request, $rules);

        $user = User::where('email','=', $request->get('email'))->first();
        if ($user != null) {
            $existing = MediaOrganizationUser::where('media_organization_id', '=', $media_org_id)->where('user_id','=',$user->id)->get();

            if (! empty($existing) && sizeof($existing) > 0) {
                return redirect()->back()->withInput()->withErrors(['email'=>'User already added.']);
            }
        }
        else {
            // We need to create and invite
            $user = new User;
            $user->name = "Unknown";
            if ($request->has('name')) $user->name = $request->get('name');
            $user->email = $request->get('email');
            $user->superuser = false;
            $user->save();

            $organization = MediaOrganization::find($media_org_id);
            $mailer = new UserMailer();
            $mailer->inviteEmail($user, $organization);
        }
        $orgUser = new MediaOrganizationUser();
        $orgUser->user_id = $user->id;
        $orgUser->media_organization_id = $media_org_id;
        $orgUser->access = $request->get('access');
        $orgUser->save();

        return redirect('/media/'.$media_org_id.'/users');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($media_org_id, $id)
    {
        $organization = MediaOrganization::find($media_org_id);
        $orgUser = MediaOrganizationUser::find($id);
        $user = User::find($orgUser->user_id);
        return view('media.users.edit', array('organization'=>$organization, 'orgUser'=>$orgUser, 'user'=>$user));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($media_org_id, $id, Request $request)
    {
        $rules = ['access'=>'required'];
        $this->validate($request, $rules);
        $orgUser = MediaOrganizationUser::find($id);
        $orgUser->access = $request->get('access');
        $orgUser->save();

        return redirect('/media/'.$media_org_id.'/users');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

}