<?php
/**
 * Created by IntelliJ IDEA.
 * User: murtazaaydogdu
 * Date: 09/03/2018
 * Time: 16:18
 */

namespace App\Http\Controllers;

use App\State;
use App\TransactionIn;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mollie_API_Client;
use Mollie_API_Exception;
use App\Http\ResponseWrapper;
use Illuminate\Support\Facades\Validator;


/**
 * Class TransactionInController
 *
 * @package App\Http\Controllers
 *
 * @SWG\Swagger(
 *     basePath="",
 *     host="localhost:8888/transaction_api",
 *     schemes={"http"},
 *     @SWG\Info(
 *         version="1.0",
 *         title="Transaction-In-In API",
 *         @SWG\Contact(name="DigitaleFactuur", url="https://digitalefactuur.nl/"),
 *     ),
 *     @SWG\Definition(
 *         definition="Transaction_In",
 *         required={"account_id", "state_id", "amount", "description", "date", "origin"},
 *         @SWG\Property(
 *             property="account_id",
 *             type="integer",
 *         ),
 *         @SWG\Property(
 *             property="state_id",
 *             type="integer"
 *         ),
 *         @SWG\Property(
 *             property="amount",
 *             type="string"
 *         ),
 *         @SWG\Property(
 *             property="description",
 *             type="string"
 *         ),
 *         @SWG\Property(
 *             property="date",
 *             type="date"
 *         ),
 *         @SWG\Property(
 *             property="origin",
 *             type="string"
 *         )
 *
 *     )
 * )
 */
class TransactionInController extends ApiController
{
    private $mollie;
    private $responseWrapper;

    public function __construct(){
        $this->middleware('auth',['except' => ['createMolliePayment']]);
        $this->responseWrapper = new ResponseWrapper();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/transaction/in",
     *     summary="Array of transaction_in",
     *     description="Returns transactions array.",
     *     operationId="api.transaction_in.index",
     *     produces={"application/json"},
     *     tags={"transaction_in"},
     *     @SWG\Response(
     *         response=200,
     *         description="transaction_in[]."
     *     ),
     *     @SWG\Response(
     *         response=401,
     *         description="Unauthorized action.",
     *     )
     * )
     */
    public function index()
    {
        try {
            $transaction = TransactionIn::where('origin', ORIGIN_NAME)->get();

            if ($transaction != null && !empty(json_decode($transaction))) {
                return $this->responseWrapper->ok($transaction);
            }
            return $this->responseWrapper->notFound(array('message' => 'The requested transactions has not been found', 'code' => 'ResourceNotFound'));
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
     *     path="/transaction/in/{id}",
     *     description="Returns transactions object.",
     *     operationId="api.transaction_in.show",
     *     produces={"application/json"},
     *     tags={"transaction_in"},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         type="integer",
     *         description="ID of transaction_in to return",
     *       ),
     *     @SWG\Response(
     *         response=200,
     *         description="Transaction-In-In overview."
     *     ),
     *     @SWG\Response(
     *         response=401,
     *         description="Unauthorized action.",
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $transaction = TransactionIn::where('origin', ORIGIN_NAME)->findOrFail($id);

            if ($transaction != null && !empty(json_decode($transaction))) {
                return $this->responseWrapper->ok($transaction);
            }
        }
        catch(ModelNotFoundException $e) {
            return $this->responseWrapper->notFound(array('message' => 'The requested state has not been found', 'code' => 'ResourceNotFound'));
        }
        catch(\Exception $e) {
            return $this->responseWrapper->serverError(array('code'=> 'UnknownError', 'stack' => $e->getMessage()));
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\POST(
     *     path="/transaction/in/store",
     *     description="Returns transactions overview.",
     *     operationId="api.transaction_in.store",
     *     produces={"application/json"},
     *     tags={"transaction_in"},
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          required=true,
     *          @SWG\Schema(
     *              @SWG\Property(
     *                 property="account_id",
     *                 type="integer",
     *              ),
     *              @SWG\Property(
     *                 property="state_id",
     *                 type="integer"
     *              ),
     *              @SWG\Property(
     *                 property="amount",
     *                 type="string"
     *              ),
     *              @SWG\Property(
     *                 property="description",
     *                 type="string"
     *              ),
     *              @SWG\Property(
     *                 property="date",
     *                 type="date"
     *              ),
     *              @SWG\Property(
     *                 property="origin",
     *                 type="string"
     *              )
     *          )
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Transaction-In-In overview."
     *     ),
     *     @SWG\Response(
     *         response=401,
     *         description="Unauthorized action.",
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->responseWrapper->badRequest(array('message' => 'The required field(s) '. $validator->errors() . ' are missing or empty from the body', 'code' => 'MissingFields'));
        }

        try {
            $transaction = new TransactionIn();
            $transaction->account_id = ACCOUNT_ID;
            $transaction->amount = $request->input('amount');
            $transaction->description = $request->input('description');
            $transaction->origin = ORIGIN_NAME;
            $check = $transaction->save();

            if ($check) {
                return $this->responseWrapper->ok($transaction);
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
     *     path="/transaction/in/edit/{id}",
     *     description="Returns transaction-in object that has been updated.",
     *     operationId="api.transaction_in.update",
     *     produces={"application/json"},
     *     tags={"transaction_in"},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         type="integer",
     *         description="ID of transaction-in to update",
     *       ),
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         description="Updated transaction object",
     *         required=true,
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="account_id",
     *                 type="integer",
     *              ),
     *              @SWG\Property(
     *                 property="state_id",
     *                 type="integer"
     *              ),
     *              @SWG\Property(
     *                 property="amount",
     *                 type="string"
     *              ),
     *              @SWG\Property(
     *                 property="description",
     *                 type="string"
     *              ),
     *              @SWG\Property(
     *                 property="date",
     *                 type="date"
     *              ),
     *              @SWG\Property(
     *                 property="origin",
     *                 type="string"
     *              )
     *        )
     *   ),
     *     @SWG\Response(
     *         response=200,
     *         description="Transaction-In-In overview."
     *     ),
     *     @SWG\Response(
     *         response=401,
     *         description="Unauthorized action.",
     *     )
     * )
     */
    public function update(Request $request, $id) {
        try {
            $transaction = TransactionIn::where('origin', ORIGIN_NAME)->findOrFail($id);

            if ($transaction) {
                $transaction->account_id = $request->input('account_id');
                $transaction->state_id = $request->input('state_id');
                $transaction->payment_id = $request->input('payment_id');
                $transaction->amount = $request->input('amount');
                $transaction->description = $request->input('description');
                $transaction->origin = $request->input('origin');
                $save = $transaction->update();

                if ($save){
                    return $this->responseWrapper->ok($transaction);
                }
            }
        }
        catch (ModelNotFoundException $e) {
            return $this->responseWrapper->notFound(array('message' => 'The requested transaction has not been found', 'code' => 'ResourceNotFound'));
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
     * @SWG\DELETE(
     *     path="/transaction/in/delete/{id}",
     *     description="Returns transaction overview.",
     *     operationId="api.transaction_in.delete",
     *     produces={"application/json"},
     *     tags={"transaction_in"},
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
    public function delete($id)
    {
        try {
            $transaction = TransactionIn::where('origin', ORIGIN_NAME)->findOrFail($id);
            $deleted = $transaction->delete();

            if ($deleted) {
                return $this->responseWrapper->ok($transaction);
            }
        }
        catch (ModelNotFoundException $e) {
            return $this->responseWrapper->notFound(array('message' => 'The requested transaction has not been found', 'code' => 'ResourceNotFound'));
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
     *     path="/transaction/in/restore/{id}",
     *     description="Returns transaction overview.",
     *     operationId="api.transaction_in.restore",
     *     produces={"application/json"},
     *     tags={"transaction_in"},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         type="integer",
     *         description="Transaction id to restore"
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
    public function restore($id)
    {
        try {
            $restored = TransactionIn::withTrashed()->findOrFail($id)->restore();

            if ($restored) {
                return $this->responseWrapper->ok($restored);
            }                
        }
        catch(ModelNotFoundException $e) {
            return $this->responseWrapper->notFound(array('message' => 'The requested transaction has not been found', 'code' => 'ResourceNotFound'));
        }
        catch (\Exception $e) {
            return $this->responseWrapper->serverError(array('code'=>'UnknownError', 'stack' => $e->getMessage()));
        }
    }
}