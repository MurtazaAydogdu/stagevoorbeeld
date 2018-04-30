<?php

namespace App\Http\Controllers;
use App\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\ResponseWrapper;

/**
 * Class StateController
 *
 * @package App\Http\Controllers
 *
 * @SWG\Swagger(
 *     basePath="",
 *     host="transaction.test",
 *     schemes={"http"},
 *     @SWG\Info(
 *         version="1.0",
 *         title="Transaction-In-In API",
 *         @SWG\Contact(name="DigitaleFactuur", url="https://digitalefactuur.nl/"),
 *     ),
 *     @SWG\Definition(
 *         definition="State",
 *         required={"name", "description"},
 *         @SWG\Property(
 *             property="name",
 *             type="string",
 *         ),
 *         @SWG\Property(
 *             property="description",
 *             type="string"
 *         )
 *     )
 * )
 */
class StateController extends ApiController
{
    private $responseWrapper;

    public function __construct()
    {
        $this->middleware('auth');
        $this->responseWrapper = new ResponseWrapper();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/states",
     *     summary="Array of states",
     *     description="Returns states array.",
     *     operationId="api.states.index",
     *     produces={"application/json"},
     *     tags={"state"},
     *     @SWG\Response(
     *         response=200,
     *         description="States[]."
     *     ),
     *     @SWG\Response(
     *         response=401,
     *         description="Unauthorized action.",
     *     )
     * )
     */
    public function index(){

        try {
            $states = State::all();

            if ($states) {
                return $this->responseWrapper->ok($states);
            }
            return $this->responseWrapper->notFound(array('message' => 'The requested state has not been found', 'code' => 'ResourceNotFound'));
        }
        catch (\Exception $e) {
            return $this->responseWrapper->serverError(array('code' => 'UnknownError', 'stack' => $e->getMessage()));
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/state/{id}",
     *     description="Returns state object.",
     *     operationId="api.states.show",
     *     produces={"application/json"},
     *     tags={"state"},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         type="integer",
     *         description="ID of state to return",
     * 	   ),
     *     @SWG\Response(
     *         response=200,
     *         description="State overview."
     *     ),
     *     @SWG\Response(
     *         response=401,
     *         description="Unauthorized action.",
     *     )
     * )
     */
    public function show($id) {

        try {
            $state = State::findOrFail($id);
            
            if ($state) {
                return $this->responseWrapper->ok($state);
            }
        }
        catch(ModelNotFoundException $e) {
            return $this->responseWrapper->notFound(array('message' => 'The requested state has not been found', 'code' => 'ResourceNotFound'));
        }
        catch (\Exception $e) {
            return $this->responseWrapper->serverError(array('code' => 'UnknownError', 'stack' => $e->getMessage()));
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\POST(
     *     path="/state/create",
     *     description="Returns states overview.",
     *     operationId="api.state.store",
     *     produces={"application/json"},
     *     tags={"state"},
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          required=true,
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="name",
     *                  type="string",
     *                  maximum=64
     *              ),
     *              @SWG\Property(
     *                  property="description",
     *                  type="string"
     *              )
     *          )
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="States overview."
     *     ),
     *     @SWG\Response(
     *         response=401,
     *         description="Unauthorized action.",
     *     )
     * )
     */
    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->responseWrapper->badRequest(array('message' => 'The required fields '. $validator->errors() . ' are missing or empty from the body', 'code' => 'MissingFields'));
        }

        try {
            $state = new State();
            $state->name = $request->input('name');
            $state->description = $request->input('description');
            $check = $state->save();

            if ($check) {
                return $this->responseWrapper->ok($state);
            }       
        }
        catch (\Exception $e) {
            return $this->responseWrapper->serverError(array('code' => 'UnknownError', 'stack' => $e->getMessage()));
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\PATCH(
     *     path="/state/edit/{id}",
     *     description="Returns state object that has been updated.",
     *     operationId="api.states.update",
     *     produces={"application/json"},
     *     tags={"state"},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         type="integer",
     *         description="ID of state to update",
     * 	   ),
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         description="Updated user object",
     *         required=true,
     *         @SWG\Schema(
     *              @SWG\Property(
     *                  property="name",
     *                  type="string",
     *                  maximum=64
     *              ),
     *              @SWG\Property(
     *                  property="description",
     *                  type="string"
     *              )
     *          )
     *   ),
     *     @SWG\Response(
     *         response=200,
     *         description="State overview."
     *     ),
     *     @SWG\Response(
     *         response=401,
     *         description="Unauthorized action.",
     *     )
     * )
     */
    public function update(Request $request, $id) {
    
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->responseWrapper->badRequest(array('message' => 'The required fields '. $validator->errors() . ' are missing or empty from the body', 'code' => 'MissingFields'));
        }

        
        try {
            $state = State::findOrFail($id);
            $state->name = $request->input('name');
            $state->description = $request->input('description');
            $check = $state->update();

            if ($check) {
                return $this->responseWrapper->ok($state);
            }
        }
        catch(ModelNotFoundException $e) {
            return $this->responseWrapper->notFound(array('message' => 'The requested state has not been found', 'code' => 'ResourceNotFound'));
        }
        catch(\Exception $e) {
            return $this->responseWrapper->serverError(array('code' => 'UnknownError', 'stack' => $e->getMessage()));
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\DELETE(
     *     path="/state/delete/{id}",
     *     description="Returns state overview.",
     *     operationId="api.state.delete",
     *     produces={"application/json"},
     *     tags={"state"},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         type="integer",
     *         description="State id to delete"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="State overview."
     *     ),
     *     @SWG\Response(
     *         response=401,
     *         description="Unauthorized action.",
     *     )
     * )
     */
    public function delete($id){
        
        try {
            $state = State::findOrFail($id);

            if ($state->delete()) {
                return $this->responseWrapper->ok($state);
            }
        }
        catch(ModelNotFoundException $e) {
            return $this->responseWrapper->notFound(array('message' => 'The requested state has not been found', 'code' => 'ResourceNotFound'));
        }

        catch (\Exception $e) {
            return $this->responseWrapper->serverError(array('code'=>'UnknownError', 'stack' => $e->getMessage()));
        }

    }

     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\DELETE(
     *     path="/state/restore/{id}",
     *     description="Returns state overview.",
     *     operationId="api.state.restore",
     *     produces={"application/json"},
     *     tags={"state"},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         type="integer",
     *         description="State id to restore"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="State overview."
     *     ),
     *     @SWG\Response(
     *         response=401,
     *         description="Unauthorized action.",
     *     )
     * )
     */
    public function restore($id) {

        try {
            $state = State::withTrashed()->findOrFail($id);

            if ($state->restore()) {
                return $this->responseWrapper->ok($state);
            }
        }
        catch(ModelNotFoundException $e) { 
            return $this->responseWrapper->notFound(array('message' => 'The requested state has not been found', 'code' => 'ResourceNotFound'));
        }
        catch (\Exception $e) {
            return $this->responseWrapper->serverError(array('code'=>'UnknownError', 'stack' => $e->getMessage()));
        }
    }
}