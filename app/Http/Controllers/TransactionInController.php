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

    public function __construct(){
        $this->middleware('auth',['except' => ['createMolliePayment']]);
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
        $transaction = TransactionIn::where('origin', ORIGIN_NAME)->get();

        if ($transaction != null && !empty(json_decode($transaction))) {
            return response()->json(['status' => 'success', 'transaction' => $transaction]);
        }
        return response()->json(['status' => 'failed', 'message' => 'No transactions found']);

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
                return response()->json(['status' => 'success', 'transaction' => $transaction]);
            }
        }
        catch(ModelNotFoundException $e) {
            return response()->json(['status' => 'failed', 'message' => 'No transactions found']);
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
        $this->validate($request, [
            'account_id' => 'required',
            'state_id' => 'required',
            'payment_id' => 'required',
            'amount' => 'required',
            'description' => 'required',
            'date' => 'required',
            'origin' => 'required',
        ]);

        $transaction = new TransactionIn();
        $transaction->account_id = $request->input('account_id');
        $transaction->state_id = $request->input('state_id');
        $transaction->payment_id = 1;
        $transaction->amount = $request->input('amount');
        $transaction->description = $request->input('description');
        $transaction->date = date('Y-m-d');
        $transaction->origin = $request->input('origin');
        $check = $transaction->save();

        if ($check) {
            return response()->json(['status' => 'success', 'message' => 'New transaction has been saved into the database']);
        }
        return response()->json(['status' => 'failed', 'message' => 'Error transaction has not been saved into the database']);
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

            if ($transaction && $this->validateTransactionInRequest($request)) {
                $transaction->account_id = $request->input('account_id');
                $transaction->state_id = $request->input('state_id');
                $transaction->payment_id = $request->input('payment_id');
                $transaction->amount = $request->input('amount');
                $transaction->description = $request->input('description');
                $transaction->origin = $request->input('origin');
                $save = $transaction->update();

                if ($save){
                    return response()->json(['status' => 'success', 'transaction' => $transaction]);
                }
                return response()->json(['status'=> 'failed', 'message' => 'Unable to update your changes']);
            }
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['status' => 'failed', 'message' => 'No transactions found']);
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
                return response()->json(['status' => 'success', 'message' => 'Transaction has been deleted']);
            }
            return response()->json(['status' => 'failed', 'messages'=> 'Unable to delete your transaction']);
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['status' => 'failed', 'message' => 'Transaction not found']);
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
                return response()->json(['status' => 'success','Transaction has been restored']);
            }                
            return response()->json(['status' => 'failed','Unable to restore the transaction']);
        }
        catch(ModelNotFoundException $e) {
            return response()->json(['status' => 'failed', 'message' => 'Transaction not found']);
        }
    }

    private function validateTransactionInRequest($request) {
        return $this->validate($request, [
            'account_id' => 'required|integer',
            'state_id' => 'required|integer',
            'payment_id' => 'required|integer',
            'amount' => 'required',
            'description' => 'required|string',
            'date' => 'required',
            'origin' => 'required|string',
        ]);
    }

    public function createMolliePayment(Request $request) {
        $this->setNewMollieApiClient($_GET['origin']);
               
        try {
            $payment = $this->getPaymentFromMollieOnId($request->id);        
        
            if ($payment->isPaid() == TRUE) {
                $transaction = new TransactionIn();
                $transaction->account_id = $_GET['account_id'];
                $transaction->payment_id = $payment->id;
                $transaction->state_id = $this->findStateIdBasedOnName($payment->status)[0]->id ;
                $transaction->amount = $payment->amount;
                $transaction->description = $payment->description;
                $transaction->date = $payment->paidDatetime;
                $transaction->origin = $_GET['origin'];
                $transaction->save();
            }
        }        
        catch (Mollie_API_Exception $e) {
            return response()->json("API call failed: " . htmlspecialchars($e->getMessage()));
        }
    }

    private function setNewMollieApiClient($origin) {
        $this->mollie = new Mollie_API_Client;
        $originSpecificApiKey = strtoupper($origin) . env('MOLLIE');
        $this->mollie->setApiKey(env($originSpecificApiKey));
    }

    private function getPaymentFromMollieOnId($id) {
        try {
            $payment = $this->mollie->payments->get($id);

            if ($payment) {
                return $payment;
            }
        } catch (Mollie_API_Exception $e) {
            return response()->json("API call failed: " . htmlspecialchars($e->getMessage()));
        }
    }
        
    private function findStateIdBasedOnName($status) {    
        return State::where('name', '=', $status)->get();
    }
}