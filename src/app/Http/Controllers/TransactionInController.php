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
use App\Http\ResponseWrapper;
use App\Http\SenderToMessageAdapter;
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
        private $responseWrapper;
        private $senderToMessageAdapter;

    public function __construct(){
        $this->middleware('auth');
        $this->responseWrapper = new ResponseWrapper();
        $this->senderToMessageAdapter = new SenderToMessageAdapter();
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
    public function index(Request $request)
    {
        try {
            $origin = $request->input('origin', $request->input('payload.origin'));

            $transaction = TransactionIn::where('origin', $origin)->get();

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
    public function show(Request $request, $id)
    {
        try {
            $origin = $request->input('origin', $request->input('payload.origin'));

            $transaction = TransactionIn::where('origin', $origin)->findOrFail($id);

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

        $origin = $request->input('origin', $request->input('payload.origin'));

        if ($validator->fails()) {
            $this->senderToMessageAdapter->send('POST', '/transaction/in/create', 'failed', $origin, $this->responseWrapper->badRequest(array('message' => 'The required fields '. $validator->errors() . ' are missing or empty from the body', 'code' => 'MissingFields')));
            return $this->responseWrapper->badRequest(array('message' => 'The required field(s) '. $validator->errors() . ' are missing or empty from the body', 'code' => 'MissingFields'));
        }

        try {
            $transaction = new TransactionIn();
            $transaction->account_id = $request->input('account_id', $request->input('payload.accountId'));
            $transaction->state_id = $request->input('state_id');
            $transaction->payment_id = $request->input('payment_id');
            $transaction->amount = $request->input('amount');
            $transaction->description = $request->input('description');
            $transaction->date = date('Y-m-d');
            $transaction->origin = $origin;
            $check = $transaction->save();

            if ($check) {
                $this->senderToMessageAdapter->send('POST', '/transaction/in/create', 'success', $origin, $this->responseWrapper->ok($transaction));
                return $this->responseWrapper->ok($transaction);
            }
        }
        catch (\Exception $e) {
            $this->senderToMessageAdapter->send('POST', '/transaction/in/create', 'failed', $origin, $this->responseWrapper->serverError(array('code' => 'UnknownError', 'stack' => $e->getMessage())));
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

            $validator = Validator::make($request->all(), [
                'amount' => 'required',
                'description' => 'required',
            ]);

            $origin = $request->input('origin', $request->input('payload.origin'));
    
            if ($validator->fails()) {
                $this->senderToMessageAdapter->send('PATCH', '/transaction/in/edit', 'failed', $origin, $this->responseWrapper->badRequest(array('message' => 'The required fields '. $validator->errors() . ' are missing or empty from the body', 'code' => 'MissingFields')));
                return $this->responseWrapper->badRequest(array('message' => 'The required field(s) '. $validator->errors() . ' are missing or empty from the body', 'code' => 'MissingFields'));
            }

            $transaction = TransactionIn::where('origin', $origin)->findOrFail($id);

            if ($transaction) {
                $transaction->amount = $request->input('amount');
                $transaction->description = $request->input('description');
                $save = $transaction->update();

                if ($save){
                    $this->senderToMessageAdapter->send('PATCH', '/transaction/in/edit', 'success', $origin, $this->responseWrapper->ok($transaction));
                    return $this->responseWrapper->ok($transaction);
                }
            }
        }
        catch (ModelNotFoundException $e) {
            $this->senderToMessageAdapter->send('PATCH', '/transaction/in/edit', 'failed', $origin, $this->responseWrapper->serverError(array('code' => 'UnknownError', 'stack' => $e->getMessage())));
            return $this->responseWrapper->notFound(array('message' => 'The requested transaction has not been found', 'code' => 'ResourceNotFound'));
        }
        catch (\Exception $e) {
            $this->senderToMessageAdapter->send('PATCH', '/transaction/in/edit', 'error', $origin, $this->responseWrapper->serverError(array('code' => 'UnknownError', 'stack' => $e->getMessage())));
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
    public function delete(Request $request, $id)
    {
        try {
            $origin = $request->input('origin', $request->input('payload.origin'));

            $transaction = TransactionIn::where('origin', $origin)->findOrFail($id);
            $deleted = $transaction->delete();

            if ($deleted) {
                $this->senderToMessageAdapter->send('DELETE', '/transaction/in/delete', 'success', $origin, $this->responseWrapper->ok($transaction));

                return $this->responseWrapper->ok($transaction);
            }
        }
        catch (ModelNotFoundException $e) {
            $this->senderToMessageAdapter->send('DELETE', '/transaction/in/delete', 'failed', $origin, $this->responseWrapper->notFound(array('message' => 'The requested transaction has not been found', 'code' => 'ResourceNotFound')));
            return $this->responseWrapper->notFound(array('message' => 'The requested transaction has not been found', 'code' => 'ResourceNotFound'));
        }
        catch(\Exception $e) {
            $this->senderToMessageAdapter->send('DELETE', '/transaction/in/delete', 'error', $origin,$this->responseWrapper->serverError(array('code' => 'UnknownError', 'stack' => $e->getMessage())));
            return $this->responseWrapper->serverError(array('code' => 'UnknownError', 'stack' => $e->getMessage()));
        }
    }
}