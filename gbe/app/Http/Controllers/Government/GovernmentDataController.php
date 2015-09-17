<?php namespace DemocracyApps\GB\Http\Controllers\Government;

use DemocracyApps\GB\Http\Controllers\Controller;

use DemocracyApps\GB\Organizations\GovernmentOrganization;
use DemocracyApps\GB\Organizations\GovernmentOrganizationUser;
use DemocracyApps\GB\Users\User;
use DemocracyApps\GB\Utility\Mailers\UserMailer;
use Illuminate\Http\Request;

class GovernmentDataController extends Controller {

    protected $governmentOrganization = null;

    public function __construct(GovernmentOrganization $org)
    {
        $this->governmentOrganization = $org;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($govt_org_id)
    {
        $organization = GovernmentOrganization::find($govt_org_id);
//        $users = GovernmentOrganizationUser::where('government_organization_id', '=', $govt_org_id)->get();

        return view('government.data.index', array('organization'=>$organization));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create($govt_org_id, Request $request)
    {
//        $organization = GovernmentOrganization::find($govt_org_id);
//        return view('government.users.create', array('organization'=>$organization));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store($govt_org_id, Request $request)
    {
//        $rules = ['email'=>'required | email', 'access'=>'required'];
//        $this->validate($request, $rules);
//
//        $user = User::where('email','=', $request->get('email'))->first();
//        if ($user != null) {
//            $existing = GovernmentOrganizationUser::where('government_organization_id', '=', $govt_org_id)->where('user_id','=',$user->id)->get();
//
//            if (! empty($existing) && sizeof($existing) > 0) {
//                return redirect()->back()->withInput()->withErrors(['email'=>'User already added.']);
//            }
//        }
//        else {
//            // We need to create and invite
//            $user = new User;
//            $user->name = "Unknown";
//            if ($request->has('name')) $user->name = $request->get('name');
//            $user->email = $request->get('email');
//            $user->superuser = false;
//            $user->save();
//
//            $organization = GovernmentOrganization::find($govt_org_id);
//            $mailer = new UserMailer();
//            $mailer->inviteEmail($user, $organization);
//        }
//        $orgUser = new GovernmentOrganizationUser();
//        $orgUser->user_id = $user->id;
//        $orgUser->government_organization_id = $govt_org_id;
//        $orgUser->access = $request->get('access');
//        $orgUser->save();
//
//        return redirect('/governments/'.$govt_org_id.'/users');
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
    public function edit($govt_org_id, $id)
    {
//        $organization = GovernmentOrganization::find($govt_org_id);
//        $orgUser = GovernmentOrganizationUser::find($id);
//        $user = User::find($orgUser->user_id);
//        return view('government.users.edit', array('organization'=>$organization, 'orgUser'=>$orgUser, 'user'=>$user));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($govt_org_id, $id, Request $request)
    {
//        $rules = ['access'=>'required'];
//        $this->validate($request, $rules);
//        $orgUser = GovernmentOrganizationUser::find($id);
//        $orgUser->access = $request->get('access');
//        $orgUser->save();
//
//        return redirect('/governments/'.$govt_org_id.'/users');

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