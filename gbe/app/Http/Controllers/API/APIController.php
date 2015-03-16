<?php namespace DemocracyApps\GB\Http\Controllers\API;
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
use Illuminate\Http\Response;
use DemocracyApps\GB\Http\Controllers\Controller;

class APIController extends Controller {

    /**
     * @var integer
     */
    protected $statusCode = Response::HTTP_OK;

    protected function setStatusAndRespond($resp) {
        if (is_array($resp) && array_key_exists('status_code',$resp)) {
            $this->setStatusCode($resp['status_code']);
            return $this->respond($resp);
        }
        else {
            return $resp;
        }
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }


    public function respond($data, $headers = [])
    {
        return \Response::json($data, $this->getStatusCode(), $headers);
    }

    public function respondWithError($message)
    {
        return $this->respond([
            'error' => [
                'message' 		=> $message,
                'status_code'	=> $this->getStatusCode()
            ]
        ]);
    }

    public function respondNotFound ($message = 'Not Found')
    {
        return $this->setStatusCode(Response::HTTP_NOT_FOUND)->respondWithError($message);
    }

    public function respondFormatError ($message = "Bad format")
    {
        return $this->setStatusCode(Response::HTTP_BAD_REQUEST)->respondWithError($message);
    }

    public function respondInternalError ($message = 'Internal Error')
    {
        return $this->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)->respondWithError($message);
    }

    public function respondFailedValidation($message = 'Failed validation')
    {
        return $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY)->respondWithError($message);
    }

    public function respondOK($message = 'Operation succeeded', $data = null)
    {
        return $this->setStatusCode(Response::HTTP_OK)->respond([
            'message' => $message,
            'status_code'	=> $this->getStatusCode(),
            'data' => $data
        ]);
    }

    public function respondCreated($message = 'Successfully created', $data)
    {
        return $this->setStatusCode(Response::HTTP_CREATED)->respond([
            'message' => $message,
            'status_code'	=> $this->getStatusCode(),
            'data' => $data
        ]);
    }

    public function respondIndex($message = 'Success', $data)
    {
        return $this->setStatusCode(Response::HTTP_OK)->respond([
            'message' => $message,
            'status_code'	=> $this->getStatusCode(),
            'data' => $data
        ]);
    }

}