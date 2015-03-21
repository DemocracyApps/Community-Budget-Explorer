<?php namespace DemocracyApps\GB\Http\Controllers\API\v1;
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
use DemocracyApps\GB\Accounts\Account;
use DemocracyApps\GB\ApiTransformers\AccountTransformer;
use DemocracyApps\GB\Http\Controllers\API\APIController;
use DemocracyApps\GB\Http\Requests;
use DemocracyApps\GB\Http\Controllers\Controller;

use DemocracyApps\GB\Organization;
use Illuminate\Http\Request;

class AccountsController extends APIController {

    private $transformer;

    public function __construct(AccountTransformer $at)
    {
        $this->transformer = $at;
    }
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($orgId)
	{
        $organization = Organization::find($orgId);
		$accounts = Account::allOrganizationAccounts($orgId);
        return $this->respondIndex('List of accounts for ' . $organization->name, $accounts, $this->transformer);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($orgId, $id)
	{
        $organization = Organization::find($orgId);
		$account = Account::find($id);

        if ($account != null) {
            return $this->respondItem($organization->name . ' Account', $account, $this->transformer);
        }
        else
            return $this->respondNotFound('No such account');
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
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
