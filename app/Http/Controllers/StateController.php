<?php

namespace App\Http\Controllers;
use App\State;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
class
StateController extends ApiController
{

    public function __construct()
    {
        $this->middleware('auth');
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
        $states = State::all();

        if ($states) {
            return response()->json(['status' => 'success', 'states' => $states]);
        }
        return response()->json(['status' => 'success', 'message' => 'Error no states found']);
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

            return response()->json(['status' => 'success', 'state' => $state]);
        }

        catch(ModelNotFoundException $e) {
            return response()->json(['status' => 'failed', 'message' => 'No states found']);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\POST(
     *     path="/state",
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

        $this->validate($request, [
            'name' => 'required',
            'description' => 'required'
        ]);

        $state = new State();
        $state->name = $request->input('name');
        $state->description = $request->input('description');
        $check = $state->save();

        if ($check) {
            return response()->json(['status' => 'success', 'message' => 'New transaction has been saved into the database']);
        }
        return response()->json(['status' => 'failed', 'message' => 'Error state has not been saved into the database']);
       
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\PATCH(
     *     path="/state/{id}",
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
        $this->validate($request, [
            'name' => 'required',
            'description' => 'required'
        ]);

        $state = State::findOrFail($id);

        $state->name = $request->input('name');
        $state->description = $request->input('description');
        $state->update();

        return response()->json(['state' => $state]);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\DELETE(
     *     path="/state/{id}",
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
        $state = State::findOrFail($id);

        $state->delete();

        return response()->json('State has been deleted');
    }
}