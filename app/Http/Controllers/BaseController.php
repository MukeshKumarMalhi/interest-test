<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BaseController extends Controller
{
    protected $ips, $user, $token, $resultArray, $postData = [];
    protected $flag = true;
    protected $statusCode = 200;

    /**
     * BaseController constructor.
     */
    // public function __construct()
    // {
    // }
    public function respondSuccess($message = 'Success!')
    {
        return $this->setStatusCode(200)->respondWithError([], true, $message);
    }

    /**
     * @param array $errors
     * @param bool $status
     * @param string $message
     * @return mixed
     */
    public function respondWithError($errors = [], $status = false, $message, $statusCode = null)
    {
        return $this->respond([], $errors, $status, $message);
    }

    /**
     * @param $data
     * @param array $headers
     * @return mixed
     */
    public function respond($data = [], $errors = [], $status, $message, $headers = [])
    {
        return response()->json(
            [
                'statusCode' => $this->getStatusCode(),
                'response' => [
                    'data' => $data
                ],
                'message' => $message,
                'status' => $status,
                'errors' => $errors
            ],
            $this->getStatusCode(),
            $headers
        );
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param $statusCode
     * @return \App\Http\Controllers\Api\BaseController
     */
    public function setStatusCode($statusCode)
    {

        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * @param array $errors
     * @param bool $status
     * @param string $message
     * @return mixed
     * 201: Object created. Useful for the store actions.
     */
    public function respondObjectCreated($errors = [], $status = false, $message = 'Object Created!')
    {
        return $this->setStatusCode(201)->respondWithError($errors, $status, $message);
    }

    /**
     * @param array $errors
     * @param bool $status
     * @param string $message
     * @return mixed
     * 204: No content. When an action was executed successfully, but there is no content to return.
     */
    public function respondNoContent($errors = [], $status = false, $message = 'No Content!')
    {
        return $this->setStatusCode(204)->respondWithError($errors, $status, $message);
    }

    /**
     * @param array $errors
     * @param bool $status
     * @param string $message
     * @return mixed
     * 206: Partial content. Useful when you have to return a paginated list of resources.
     */
    public function respondPartialContent($errors = [], $status = false, $message = 'Partial Content!')
    {
        return $this->setStatusCode(206)->respondWithError($errors, $status, $message);
    }

    /**
     * @param array $errors
     * @param bool $status
     * @param string $message
     * @return mixed
     * 400: Bad request. The standard option for requests that fail to pass validation.
     */
    public function respondBadRequest($errors = [], $status = false, $message = 'Bad Request!')
    {
        return $this->setStatusCode(400)->respondWithError($errors, $status, $message);
    }

    /**
     * @param array $errors
     * @param bool $status
     * @param string $message
     * @return mixed
     * 401: Unauthorized. The user needs to be authenticated.
     */
    public function respondUnauthorized($errors = [], $status = false, $message = 'Unauthorized!')
    {
        return $this->setStatusCode(401)->respondWithError($errors, $status, $message);
    }

    /**
     * @param array $errors
     * @param bool $status
     * @param string $message
     * @return mixed
     * 403: Forbidden. The user is authenticated, but does not have the permissions to perform an action.
     */
    public function respondForbidden($errors = [], $status = false, $message = 'Forbidden!')
    {
        return $this->setStatusCode(403)->respondWithError($errors, $status, $message);
    }

    /**
     * @param array $errors
     * @param bool $status
     * @param string $message
     * @return mixed
     * 404: Not found. This will be returned automatically by Laravel when the resource is not found.
     */
    public function respondNotFound($errors = [], $status = false, $message = 'Records Not Found!', $data = [])
    {
        return response()->json(
            [
                'statusCode' => 204,
                'response' => [
                    'data' => $data
                ],
                'message' => $message,
                'status' => $status,
                'errors' => $errors
            ],
            $this->getStatusCode()
        );
    }

    /**
     * @param array $errors
     * @param bool $status
     * @param string $message
     * @return mixed
     * 409: Method Already Exits.Aka Conflict Message.
     */

    public function respondMethodAlreadyExits($errors = [], $status = false, $message = 'Method Not Allowed!')
    {

        return $this->setStatusCode(409)->respondWithError($errors, $status, $message);
    }

    /**
     * @param array $errors
     * @param bool $status
     * @param string $message
     * @return mixed
     * 405: Method Not Allowed. The request method is known by the server but is not supported by the target resource.
     */
    public function respondMethodNotAllowed($errors = [], $status = false, $message = 'Method Not Allowed!')
    {
        return $this->setStatusCode(405)->respondWithError($errors, $status, $message);
    }

    /**
     * @param array $errors
     * @param bool $status
     * @param string $message
     * @return mixed
     * 500: Internal server error. Ideally you're not going to be explicitly returning this, but if something unexpected breaks, this is what your user is going to receive.
     */
    public function respondInternalError($errors = [], $status = false, $message = 'Internal Error!')
    {
        return $this->setStatusCode(500)->respondWithError($errors, $status, $message);
    }

    /**
     * @param string $message
     * @return mixed
     * 503: Service unavailable. Pretty self explanatory, but also another code that is not going to be returned explicitly by the application.
     */
    public function respondServiceUnavailable($message = 'Service Unavailable!')
    {

        return $this->setStatusCode(503)->respondWithError($message);
    }

    /**
     * @param $userId
     * @param $methodNameCreatedFor
     * @return mixed
     */
    protected function createUserToken($user, $methodNameCreatedFor)
    {
        /** @var TYPE_NAME $user . created user */
        $this->user = $user;
        /** @var TYPE_NAME $methodNameCreatedFor */
        $this->token = $this->user->createToken($methodNameCreatedFor)->plainTextToken;
        /** @var TYPE_NAME $this */
        return $this->token;
    }

    /**
     * @param array $request
     * @param array $validationRules
     * @return mixed
     */
    protected function responseValidation(array $request, array $validationRules, array $validationMessages)
    {
        /** @var TYPE_NAME $request */
        /** @var TYPE_NAME $validationRules */
        return Validator::make($request, $validationRules, $validationMessages);
    }

    /**
     * @param array $errors
     * @return mixed
     */
    protected function validationErrors(array $errors)
    {
        /** @var TYPE_NAME $errors */
        foreach ($errors as $error) {
            /** @var TYPE_NAME $this */
            $this->resultArray[] = $error;
        }
        return $this->resultArray;
    }

    protected function skippedElementArray(array $skippedArray, array $postData)
    {
        foreach ($postData as $key => $value) {
            if (!in_array($key, $skippedArray)) {
                $this->postData[$key] = $value;
            }
        }

        return $this->postData;
    }

    /**
     * @param array $rules
     * @param string $request
     * @return array|int[]
     */
    protected function validateInput($rules = [], $request = '')
    {
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return [
                'message' => $validator->messages(),
                'error' => 1
            ];
        } else {
            return ['error' => 0];
        }
    }
}
