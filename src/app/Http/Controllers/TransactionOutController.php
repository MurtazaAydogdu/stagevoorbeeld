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

    public function __construct(TransactionInInterface $transactionRepo){
        $this->middleware('auth');
        $this->responseWrapper = new ResponseWrapper();
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
    public function show($id){

        try {
            $transaction = TransactionOut::where('origin', ORIGIN_NAME)->where('account_id', $id);
    
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
        return $this->getSubscripionRulesException($request->description);  
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
            return $this->responseWrapper->notFound(array('message' => 'The requested transactions has not been found', 'code' => 'ResourceNotFound'));
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
            return $this->responseWrapper->notFound(array('message' => 'The requested transactions has not been found', 'code' => 'ResourceNotFound'));
        }
        catch (\Exception $e) {
            return $this->responseWrapper->serverError(array('code' => 'UnknownError', 'stack' => $e->getMessage()));
        }
    }

    private function getSubscripionRulesException($description) {
        $exception = $this->getFromSubscriptionApi('/exceptions/', ACCOUNT_ID);
       
        $arr = json_decode($exception, true);

        if ($arr['status'] === 'success') {
            return $this->checkSubscriptionType($arr['data'], $description);
        }
        else {
            return $this->getSubscripionRules($description);
        }
    }

    private function checkSubscriptionType($arr, $description) {

        $selectedObj = reset($arr);

        //get the transaction out the transaction_out table
        $transaction = $this->getTransactionByAccountIdAndDate($selectedObj['time_period']);
    
        $total = 0;
        foreach($transaction as $value) {
            if ($value->subscription_id === $selectedObj['subscription_id']) {
                $total++;
            }
        }

        if ($total < $selectedObj['quantity']) {
            return $this->saveTransactionToDatabase(0, $description, $selectedObj['subscription_id']); 
        }
        else {
            $tmpArr = $arr;
            $tmpArr = array_slice($tmpArr, 1, 1);

            if (!empty($tmpArr)) {
                return $this->checkSubscriptionType($tmpArr, $description);
            }
            else {
                return $this->checkIfUserHasEnoughAmountInWallet($selectedObj, $description);
            }
        }
    }

    private function getSubscripionRules ($description) {
        $accountSubscription = $this->getFromSubscriptionApi('/account/subscriptions/', ACCOUNT_ID);
            
        $decodedAccountSubscription = json_decode($accountSubscription, true);

        $rules = $this->getFromSubscriptionApi('/rules/', $decodedAccountSubscription['data']['subscription_id']);

        $decodedRules = json_decode($rules, true);

        $transaction = $this->getTransactionByAccountId();

        if (count($transaction) < $decodedRules['data'][0]['quantity']) {
            return $this->saveTransactionToDatabase(0, $description, $decodedRules['subscription_id']);
        }
        else {
            $test = $this->checkIfUserHasEnoughAmountInWallet($decodedRules['data'][0], $description);
            var_dump($test);
        }
    }

    private function getTransactionByAccountIdAndDate($time_period) {
        try {
            $trans =  TransactionOut::where('origin', ORIGIN_NAME)
                ->where('account_id', ACCOUNT_ID)
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

    private function getTransactionByAccountId() {
        try {
            $trans =  TransactionOut::where('origin', ORIGIN_NAME)
                ->where('account_id', ACCOUNT_ID)
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

        $response = $client->get('192.168.60.103:8000' . $url . $id,[
            'headers' => [
                'authorization' => env('ACCESS_TOKEN')
            ]
        ]);
        return $response->getBody()->getContents();
    }

    private function saveTransactionToDatabase($price, $description, $subscription_id) {
        try {
            $transaction = new TransactionOut();
            $transaction->account_id = ACCOUNT_ID;
            $transaction->state_id = 1;
            $transaction->subscription_id = $subscription_id;
            $transaction->amount = $price;
            $transaction->description = $description;
            $transaction->date = date('Y-m-d');
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

    private function checkIfUserHasEnoughAmountInWallet($obj, $description) {
        //get transaction_in on account_id
        $transaction = $this->transactionRepo->get(ACCOUNT_ID);

        $totalTransactionIn = 0;
        foreach (json_decode($transaction, true) as $value) {
            $totalTransactionIn += $value['amount'];
        }

        // get transaction_out on accout_id
        $transactionOut = $this->getTransactionByAccountId();

        $totalTransactionOut = 0;
        foreach(json_decode($transactionOut, true) as $value) {
            $totalTransactionOut += $value['amount'];
        }
    
        if ( $totalTransactionIn < ($totalTransactionOut + $obj['price'])) {
            return $this->responseWrapper->ok('Insufficient balance in the wallet');
        }
        else {
            return $this->saveTransactionToDatabase($obj['price'], $description, $obj['subscription_id']);
        }
    }
}