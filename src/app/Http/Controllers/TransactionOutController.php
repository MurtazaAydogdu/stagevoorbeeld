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
use GuzzleHttp\Client;
use App\Http\ResponseWrapper;
use App\Http\RabbitMQ;
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
    private $transactionRepo;
    private $rabbitMQ;

    public function __construct(TransactionInInterface $transactionRepo){
        $this->middleware('auth');
        $this->responseWrapper = new ResponseWrapper();
        $this->rabbitMQ = new RabbitMQ();
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
    public function index(){
    
        try {
            $transaction = TransactionOut::where('origin', ORIGIN_NAME)->get();

            if ($transaction !=null && !empty(json_decode($transaction))) {
                return $this->rabbitMQ->send($this->responseWrapper->ok($transaction));
                // return $this->responseWrapper->odobik($transaction);
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
    public function show($id){

        try {
            $transaction = TransactionOut::where('origin', ORIGIN_NAME)->where('account_id', $id)->get();
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
            'description' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->responseWrapper->badRequest(array('message' => 'The required fields '. $validator->errors() . ' are missing or empty from the body', 'code' => 'MissingFields'));
        }
        return $this->getSubscriptionRulesException($request->description);  
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
            'account_id' => 'required',
            'state_id' => 'required',
            'amount' => 'required',
            'description' => 'required',
            'origin' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->responseWrapper->badRequest(array('message' => 'The required fields '. $validator->errors() . ' are missing or empty from the body', 'code' => 'MissingFields'));
        }

        try {
            $transaction = TransactionOut::where('origin', ORIGIN_NAME)->findOrFail($id);

            if ($transaction) {
                $transaction->account_id = ACCOUNT_ID;
                $transaction->state_id = 2;
                $transaction->amount = $request->amount;
                $transaction->description = $request->description;
                $transaction->origin = ORIGIN_NAME;
                $updated = $transaction->update();

                if ($updated) {
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
    public function delete($id){
        try {
            $transaction = TransactionOut::where('origin', ORIGIN_NAME)->findOrFail($id);

            $deleted = $transaction->delete();

            if ($deleted) {
                return $this->responseWrapper->ok($transaction);
            }
        }
        catch(ModelNotFoundException $e) {
            return $this->responseWrapper->notFound(array('message' => 'The requested transaction has not been found', 'code' => 'ResourceNotFound'));
        }

        catch (\Exception $e) {
            return $this->responseWrapper->serverError(array('code' => 'UnknownError', 'stack' => $e->getMessage()));
        }
    }

    public function restore($id) {
        try {
            $transaction = TransactionOut::withTrashed()->findOrFail($id);

            $restored = $transaction->restore();

            if ($restored) {
                return $this->responseWrapper->ok($transaction);
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
     * This function is responsible for retrieving all subscriptionRulesException from the subscription api. 
     * In a success case, the checkTotalSubscriptionsAndSave is called and otherwise the subscriptionRules.
     */
    private function getSubscriptionRulesException($description) {
        $exception = $this->getFromSubscriptionApi('/exceptions/', ACCOUNT_ID);
       
        $arr = json_decode($exception, true);

        if ($arr['status'] === 'success') {
            return $this->checkTotalSubscriptionsAndSave($arr['data'], $description);
        }
        else {
            return $this->getSubscriptionRulesCheckTheAmountFromTheDatabaseAndSaveTransaction($description);
        }
    }

    /** 
     * This function is responsible for retrieving all transactionOut based on account_id and time_period (month, quarter and year). Subsequently, 
     * it is counted how often this occurs in the table so that a comparison can be made with the retrieved quantity of the subscriptionExceptionRules. 
     * If the number in the database is smaller than the quantity of the subscriptionExceptionRules, this means that the user can send the prodcut (free). 
     * It also looks recursively whether the user has more subscriptionExceptionRules or not. If not, the checkIfUserHasEnoughTransactionOutAmountAndSave function is called.
     */
    private function checkTotalSubscriptionsAndSave($arr, $description) {

        //get the first element uit the array.
        // $selectedObj = reset($arr);
        $selectedObj = array_pop($arr);

        //get the transaction out the transaction_out table
        $transaction = $this->getTransactionByAccountIdAndDate(ACCOUNT_ID, $selectedObj['time_period']);
    
        $totalSubscriptions = 0;
        foreach($transaction as $value) {
            if ($value->subscription_id === $selectedObj['subscription_id']) {
                $totalSubscriptions++;
            }
        }

        if ($totalSubscriptions < $selectedObj['quantity']) {
            return $this->saveTransactionToDatabase(0, $description, $selectedObj['subscription_id'], $selectedObj['product_id']); 
        }
        else {
            $tmpArr = $arr;
            $tmpArr = array_slice($tmpArr, 1, 1);

            if (!empty($tmpArr)) {
                return $this->checkSubscriptionType($tmpArr, $description);
            }
            else {
                return $this->checkIfUserHasEnoughTransactionOutAmountAndSave($selectedObj, $description);
            }
        }
    }

    /**
     * This function is primarily responsible for saving a transaction if the user has enough amount of subscriptions. First the subscription_id will get from the the subscriptions api.
     * Secondly the rules will be retrieved from the rules table by the subscription_id. Thirdly the all the transactionOut will be retrieved based on the time_period and account_id.
     * Finally, it also counts how often this rule occurs in the database and then checks whether the user can still send a sufficient number of products. 
     * Otherwise, the checkIfUserHasEnoughTransactionOutAmountAndSave function is called again.
     */
    private function getSubscriptionRulesCheckTheAmountFromTheDatabaseAndSaveTransaction ($description) {
        $accountSubscription = $this->getFromSubscriptionApi('/account/subscriptions/', ACCOUNT_ID);

        $decodedAccountSubscription = json_decode($accountSubscription, true);

        $rules = $this->getFromSubscriptionApi('/rules/', \__::get($decodedAccountSubscription, 'data.0.subscription_id'));

        $decodedRules = json_decode($rules, true);
       
        $transaction = $this->getTransactionByAccountIdAndDate(ACCOUNT_ID, \__::get($decodedRules, 'data.0.time_period'));

        $amountSubscription = \__::get($decodedRules, 'data.0.subscription_id');

        $totalSubscriptions = 0;
        foreach ($transaction as $value) {
            if ($value->subscription_id === $amountSubscription) {
                $totalSubscriptions++;
            }
        }

        if ($totalSubscriptions < $amountSubscription) {
            return $this->saveTransactionToDatabase(0, $description, $amountSubscription, \__::get($decodedRules, 'data.0.product_id'));
        }
        else {
            return $this->checkIfUserHasEnoughTransactionOutAmountAndSave(\__::get($decodedRules, 'data.0'), $description);
        }
    }

    private function getTransactionByAccountIdAndDate($account_id, $time_period) {
        try {
            $trans =  TransactionOut::where('origin', ORIGIN_NAME)
                ->where('account_id', $account_id)
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

    private function getTransactionByAccountId($account_id) {
        try {
            $trans =  TransactionOut::where('origin', ORIGIN_NAME)
                ->where('account_id', $account_id)
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

    private function getFromSubscriptionApi($url, $id) {
        $client = new Client();

        $response = $client->get(env('SUBSCRIPTION_API_URL') . $url . $id,[
            'headers' => [
                'authorization' => env('ACCESS_TOKEN')
            ]
        ]);
        return $response->getBody()->getContents();
    }

    private function saveTransactionToDatabase($price, $description, $subscription_id, $product_id) {
        try {
            $transaction = new TransactionOut();
            $transaction->account_id = ACCOUNT_ID;
            $transaction->subscription_id = $subscription_id;
            $transaction->product_id = $product_id;
            $transaction->amount = $price;
            $transaction->description = $description;
            $transaction->origin = ORIGIN_NAME;
            $saved = $transaction->save();

            if ($saved) {
                return $this->responseWrapper->ok($transaction);
            }
        }
        catch (\Exception $e) {
            return $this->responseWrapper->serverError(array('code' => 'UnknownError', 'stack' => $e->getMessage()));
        }
    }

    /**
     * This function is responsible for checking if the transactionIn amount is smaller then the transactionOut amount. If this is true then it will return 
     * 'has insufficient balance'.Otherwise a new transactionOut is writen to the database with the price of the product
     */
    private function checkIfUserHasEnoughTransactionOutAmountAndSave($obj, $description) {
        
        $totalTransactionIn = $this->countTransactionInByAccountId();

        $totalTransactionOut = $this->countTransactionOutByAccountId();
       
    
        if ( $totalTransactionIn < ($totalTransactionOut + $obj['price'])) {
            return $this->responseWrapper->reject(array('message' => 'The requested feature is currently unavailable because of insufficient balance for transaction', 'code' => 'FeatureUnavailable'));
        }
        else {
            return $this->saveTransactionToDatabase($obj['price'], $description, $obj['subscription_id'], $obj['product_id']);
        }
    }
    
    /**
     * This function is responsible for retrieving all the transactionIn based on the account_id and 
     * counting the amount of the transactionIns.
     */
    private function countTransactionInByAccountId() {
        //get transaction_in on account_id
        $transaction = $this->transactionRepo->get(ACCOUNT_ID);

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
    private function countTransactionOutByAccountId() {
         // get transaction_out on account_id
         $transactionOut = $this->getTransactionByAccountId(ACCOUNT_ID);

         $totalTransactionOut = 0;
         foreach(json_decode($transactionOut, true) as $value) {
             $totalTransactionOut += $value['amount'];
         }

         return $totalTransactionOut;
    }
}