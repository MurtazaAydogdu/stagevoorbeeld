<?php
/**
 * Created by IntelliJ IDEA.
 * User: murtazaaydogdu
 * Date: 13/03/2018
 * Time: 11:28
 */

namespace App\Http\Controllers;

use App\TransactionOut;
use App\TransactionIn;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\ResponseWrapper;
use App\Http\SenderToMessageAdapter;
use App\Interfaces\TransactionInInterface;
use Illuminate\Support\Facades\Validator;
require_once __DIR__.'/../../../vendor/autoload.php';

/**
 * Class TransactionOutController
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
 *         definition="Transaction_Out",
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
class TransactionOutController extends ApiController
{
    private $responseWrapper;
    private $senderToMessageAdapter;
    private $transactionRepo;

    public function __construct(TransactionInInterface $transactionRepo){
        $this->middleware('auth');
        $this->responseWrapper = new ResponseWrapper();
        $this->senderToMessageAdapter = new SenderToMessageAdapter();
        $this->transactionRepo = $transactionRepo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/transaction/out",
     *     summary="Array of transaction_out",
     *     description="Returns transactions array.",
     *     operationId="api.transaction_out.index",
     *     produces={"application/json"},
     *     tags={"transaction_out"},
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
    public function index(Request $request){
    
        try {
            $origin = $request->input('payload.origin');

            $transaction = TransactionOut::where('origin', $origin)->get();

            if ($transaction !=null && !empty(json_decode($transaction))) {
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
     *     path="/transaction/out/{id}",
     *     description="Returns transactions object.",
     *     operationId="api.transaction_out.show",
     *     produces={"application/json"},
     *     tags={"transaction_out"},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         type="integer",
     *         description="ID of transaction_out to return",
     * 	   ),
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
    public function show(Request $request, $id){

        try {
            $origin = $request->input('payload.origin');

            $transaction = TransactionOut::where('origin', $origin)->where('account_id', $id)->get();
            if ($transaction) {
                return $this->responseWrapper->ok($transaction);
            }
        }
        catch (ModelNotFoundException $e) {
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
     * @SWG\POST(
     *     path="/transaction-out",
     *     description="Returns transactions overview.",
     *     operationId="api.transaction_out.store",
     *     produces={"application/json"},
     *     tags={"transaction_out"},
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
    public function store(Request $request) {

        $validator = Validator::make($request->all(), [
            'description' => 'required',
            'data' => 'required|array'
        ]);

        $origin = $request->input('payload.origin');


        if ($validator->fails()) {
            $this->senderToMessageAdapter->send('POST', '/transaction/out/create', 'failed', $origin, $this->responseWrapper->badRequest(array('message' => 'The required fields '. $validator->errors() . ' are missing or empty from the body', 'code' => 'MissingFields')));
            return $this->responseWrapper->badRequest(array('message' => 'The required fields '. $validator->errors() . ' are missing or empty from the body', 'code' => 'MissingFields'));
        }
        return $this->checkTotalSubscriptionsAndSave($request->data, $request);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\PATCH(
     *     path="/transaction-out/{id}",
     *     description="Returns transaction-out object that has been updated.",
     *     operationId="api.transaction_out.update",
     *     produces={"application/json"},
     *     tags={"transaction_out"},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         type="integer",
     *         description="ID of transaction-out to update",
     * 	   ),
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         description="Updated transaction object",
     *         required=true,
     *         @SWG\Schema(
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

        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'subscription_id' => 'required',
            'amount' => 'required',
            'description' => 'required'
        ]);

        $origin = $request->input('payload.origin');


        if ($validator->fails()) {
            $this->senderToMessageAdapter->send('PATCH', '/transaction/out/edit', 'failed', $origin, $this->responseWrapper->badRequest(array('message' => 'The required fields '. $validator->errors() . ' are missing or empty from the body', 'code' => 'MissingFields')));
            return $this->responseWrapper->badRequest(array('message' => 'The required fields '. $validator->errors() . ' are missing or empty from the body', 'code' => 'MissingFields'));
        }

        try {
            $transaction = TransactionOut::where('origin', $origin)->findOrFail($id);

            if ($transaction) {
                $transaction->product_id = $request->product_id;
                $transaction->subscription_id = $request->subscription_id;
                $transaction->amount = $request->amount;
                $transaction->description = $request->description;
                $updated = $transaction->update();

                if ($updated) {
                    $this->senderToMessageAdapter->send('PATCH', '/transaction/out/edit', 'success', $origin, $this->responseWrapper->ok($transaction));
                    return $this->responseWrapper->ok($transaction);
                }
            }
        }

        catch (ModelNotFoundException $e) {
            $this->senderToMessageAdapter->send('PATCH', '/transaction/out/edit', 'failed', $origin, $this->responseWrapper->notFound(array('message' => 'The requested transaction has not been found', 'code' => 'ResourceNotFound')));
            return $this->responseWrapper->notFound(array('message' => 'The requested transaction has not been found', 'code' => 'ResourceNotFound'));
        }

        catch (\Exception $e) {
            $this->senderToMessageAdapter->send('PATCH', '/transaction/out/edit', 'error', $origin, $this->responseWrapper->serverError(array('code' => 'UnknownError', 'stack' => $e->getMessage())));
            return $this->responseWrapper->serverError(array('code' => 'UnknownError', 'stack' => $e->getMessage()));
        }

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\DELETE(
     *     path="/transaction/out/delete/{id}",
     *     description="Returns transaction overview.",
     *     operationId="api.transaction_out.delete",
     *     produces={"application/json"},
     *     tags={"transaction_out"},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         type="integer",
     *         description="Transaction-In-In id to delete"
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
    public function delete(Request $request, $id){
        
        try {
            $origin = $request->input('payload.origin');

            $transaction = TransactionOut::where('origin', $origin)->findOrFail($id);

            $deleted = $transaction->delete();

            if ($deleted) {
                $this->senderToMessageAdapter->send('DELETE', '/transaction/out/delete', 'success', $origin, $this->responseWrapper->ok($transaction));
                return $this->responseWrapper->ok($transaction);
            }
        }
        catch(ModelNotFoundException $e) {
            $this->senderToMessageAdapter->send('DELETE', '/transaction/out/delete', 'failed', $origin, $this->responseWrapper->notFound(array('message' => 'The requested transaction has not been found', 'code' => 'ResourceNotFound')));
            return $this->responseWrapper->notFound(array('message' => 'The requested transaction has not been found', 'code' => 'ResourceNotFound'));
        }

        catch (\Exception $e) {
            $this->senderToMessageAdapter->send('DELETE', '/transaction/out/delete', 'error', $origin, $this->responseWrapper->serverError(array('code' => 'UnknownError', 'stack' => $e->getMessage())));
            return $this->responseWrapper->serverError(array('code' => 'UnknownError', 'stack' => $e->getMessage()));
        }
    }

    private function checkRabbitValues($data, $request) {
        if (!empty($data['rabbit_account_id']) && !empty($data['rabbit_origin'])) {
            return [$data['rabbit_account_id'], $data['rabbit_origin']];
        }
        else {
            return [$request->input('payload.accountId'), $request->input('payload.origin')];
        }
    }


    /** 
     * This function is responsible for retrieving all transactionOut based on account_id and time_period (month, quarter and year). Subsequently, 
     * it is counted how often this occurs in the table so that a comparison can be made with the retrieved quantity of the subscriptionExceptionRules. 
     * If the number in the database is smaller than the quantity of the subscriptionExceptionRules, this means that the user can send the prodcut (free). 
     * It also looks recursively whether the user has more subscriptionExceptionRules or not. If not, the checkIfUserHasEnoughTransactionOutAmountAndSave function is called.
     */
    private function checkTotalSubscriptionsAndSave($arrRules, $request) {

        //get the last element uit the array (account_id and origin);
        $data = end($arrRules);

        $description = $request->input('description');
       
        //check whether the request come from rabbit or from a rest call.
        $res = $this->checkRabbitValues($data, $request);

        //get the first element uit the array.
        $selectedObj = array_shift($arrRules);

        //get the transaction out the transaction_out table. $res[0] and $res[1] are equal to the value of ACCOUNT_ID and ORIGIN NAME OR RABBIT_ACCOUNT_ID and RABBIT_ORIGIN. 
        $transaction = $this->getTransactionByAccountIdAndDate($res[0] ,$res[1], $selectedObj['time_period']);
    
        $totalSubscriptions = 0;
        foreach($transaction as $value) {
            if ($value->subscription_id === $selectedObj['subscription_id']) {
                $totalSubscriptions++;
            }
        }

        if ($totalSubscriptions < $selectedObj['quantity']) {
            // $res[0] and $res[1] are equal to the value of ACCOUNT_ID and ORIGIN NAME OR RABBIT_ACCOUNT_ID and RABBIT_ORIGIN. 
            return $this->saveTransactionToDatabase(0, $res[0], $res[1], $description, $selectedObj['subscription_id'], $selectedObj['product_id']); 
        }
        else {
            $tmpArrRules = $arrRules;

            if (sizeof($tmpArrRules) === 1) {
                $tmpArrRules = [];
            }

            if (!empty($tmpArrRules)) {
                return $this->checkTotalSubscriptionsAndSave($tmpArrRules, $request);
            }
            else {
                // $res[0] and $res[1] are equal to the value of ACCOUNT_ID and ORIGIN NAME OR RABBIT_ACCOUNT_ID and RABBIT_ORIGIN. 
                return $this->checkIfUserHasEnoughTransactionOutAmountAndSave($selectedObj, $res[0], $res[1], $description);
            }
        }
    }

    private function getTransactionByAccountIdAndDate($accountId,$origin, $time_period) {
        try {
            $trans =  TransactionOut::where('origin', $origin)
                ->where('account_id', $accountId)
                ->where('date', '>=', ($time_period === 'quarter' ? date(sprintf('Y-%s-01', floor((date('n') - 1) / 3) * 3 + 1)) : date('Y-m')))
                ->where('date', '<=', ($time_period === 'quarter' ? date(sprintf('Y-%s-t', floor((date('n') + 2) / 3) * 3)) : date('Y-m', strtotime('+1 '. $time_period))))
                ->get();
            return $trans;
        }
        catch (ModelNotFoundException $e) {
            return $this->responseWrapper->notFound(array('message' => 'The requested transactions has not been found', 'code' => 'ResourceNotFound'));
        }

        catch (\Exception $e) {
            return $this->responseWrapper->serverError(array('code' => 'UnknownError', 'stack' => $e->getMessage()));
        }
    }

    private function getTransactionByAccountId($accountId, $origin) {
        try {
            $trans =  TransactionOut::where('origin', $origin)
                ->where('account_id', $accountId)
                ->get();

            return $trans;
        }
        catch (ModelNotFoundException $e) {
            return $this->responseWrapper->notFound(array('message' => 'The requested transactions has not been found', 'code' => 'ResourceNotFound'));
        }

        catch (\Exception $e) {
            return $this->responseWrapper->serverError(array('code' => 'UnknownError', 'stack' => $e->getMessage()));
        }
    }

    private function saveTransactionToDatabase($price, $accountId,$origin, $description, $subscription_id, $product_id) {
        try {
            $transaction = new TransactionOut();
            $transaction->account_id = $accountId;
            $transaction->subscription_id = $subscription_id;
            $transaction->product_id = $product_id;
            $transaction->amount = $price;
            $transaction->description = $description;
            $transaction->origin = $origin;
            $saved = $transaction->save();

            if ($saved) {
                $this->senderToMessageAdapter->send('POST', '/transaction/out/create', 'success', $origin, $this->responseWrapper->ok($transaction));
                return $this->responseWrapper->ok($transaction);
            }
        }
        catch (\Exception $e) {
            $this->senderToMessageAdapter->send('POST', '/transaction/out/create', 'error', $origin, $this->responseWrapper->serverError(array('code' => 'UnknownError', 'stack' => $e->getMessage())));
            return $this->responseWrapper->serverError(array('code' => 'UnknownError', 'stack' => $e->getMessage()));
        }
    }

    /**
     * This function is responsible for checking if the transactionIn amount is smaller then the transactionOut amount. If this is true then it will return 
     * 'has insufficient balance'.Otherwise a new transactionOut is writen to the database with the price of the product
     */
    private function checkIfUserHasEnoughTransactionOutAmountAndSave($obj,$accountId, $origin, $description) {
        
        $totalTransactionIn = $this->countTransactionInByAccountId($accountId, $origin);

        $totalTransactionOut = $this->countTransactionOutByAccountId($accountId, $origin);
    
        if ( $totalTransactionIn < ($totalTransactionOut + $obj['price'])) {
            $this->senderToMessageAdapter->send('POST', '/transaction/out/create', 'error', $origin, $this->responseWrapper->reject(array('message' => 'The requested feature is currently unavailable because of insufficient balance for transaction', 'code' => 'FeatureUnavailable')));
            return $this->responseWrapper->reject(array('message' => 'The requested feature is currently unavailable because of insufficient balance for transaction', 'code' => 'FeatureUnavailable'));
        }
        else {
            return $this->saveTransactionToDatabase($obj['price'], $accountId, $origin, $description, $obj['subscription_id'], $obj['product_id']);
        }
    }
    
    /**
     * This function is responsible for retrieving all the transactionIn based on the account_id and 
     * counting the amount of the transactionIns.
     */
    private function countTransactionInByAccountId($accountId) {
        //get transaction_in on account_id
        $transaction = $this->transactionRepo->get($accountId);

        $totalTransactionIn = 0;
        foreach (json_decode($transaction, true) as $value) {
            $totalTransactionIn += $value['amount'];
        }

        return $totalTransactionIn;
    }
    
    /**
     * This function is responsible for retrieving all the transactionOut based on the account_id and 
     * counting the amount of the transactionOuts.
     */
    private function countTransactionOutByAccountId($accountId, $origin) {
         // get transaction_out on account_id
         $transactionOut = $this->getTransactionByAccountId($accountId, $origin);

         $totalTransactionOut = 0;
         foreach(json_decode($transactionOut, true) as $value) {
             $totalTransactionOut += $value['amount'];
         }

         return $totalTransactionOut;
    }
}