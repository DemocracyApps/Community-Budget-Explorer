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

use DemocracyApps\GB\Accounts\Dataset;
use DemocracyApps\GB\ApiTransformers\DatasetTransformer;
use DemocracyApps\GB\Http\Controllers\API\APIController;
use DemocracyApps\GB\Http\Requests;
use DemocracyApps\GB\Http\Controllers\Controller;

use DemocracyApps\GB\Organization;
use Illuminate\Http\Request;

class DatasetsController extends APIController {
    private $transformer;

    public function __construct(DatasetTransformer $t)
    {
        $this->transformer = $t;
    }

    /**
     * Display a listing of the resource.
     *
     * @param $orgId
     * @return Response
     */
	public function index($orgId)
	{
        $organization = Organization::find($orgId);
        $datasets = Dataset::where('organization','=',$orgId)->get();
        return $this->respondIndex('List of datasets for ' . $organization->name, $datasets,
            $this->transformer, ['includeData'=>false]);
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
     * @param $orgId
     * @param  int $id
     * @param Request $request
     * @return Response
     */
    // http://gbe.dev/api/v1/organizations/1/datasets/1?noMapping=true
	public function show($orgId, $dsId, Request $request)
	{
        $organization = Organization::find($orgId);

        if ($organization == null) return $this->respondNotFound('No such organization');

        $params = array();
        if ($request->has('noMapping') && strtolower($request->get('noMapping') == 'true')) {
            $params['noMapping'] = true;
        }
        if ($request->has('type')) {
            $params['type'] = $request->get('type');
        }

        $idList = explode(',', $dsId);

        $datasetList = array();
        foreach ($idList as $id) {
            $dataset = Dataset::find($id);
            if ($dataset == null) return $this->respondNotFound('No such dataset ' . $id);
            $dataset = $this->transformer->transform($dataset, $params);
            $datasetList[] = $dataset;
        }

        return $this->respondItem('Dataset of ' . $organization->name, $datasetList);

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
