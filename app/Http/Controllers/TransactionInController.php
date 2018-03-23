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
require_once dirname(__FILE__) . "/../../../vendor/mollie/mollie-api-php/src/Mollie/API/Autoloader.php";

/**
 * Class TransactionInController
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
        $this->middleware('auth', ['except' => ['checkStates']]);
        $this->setNewMollieApiClient();
    }

    private function setNewMollieApiClient() {
        $this->mollie = new Mollie_API_Client;
        $this->mollie->setApiKey(env('MOLLIE_TEST_API_KEY'));
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/transaction-in",
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
            $payments = $this->mollie->payments->all();

            return response()->json($payments);
        } catch (Mollie_API_Exception $e) {
            return response()->json("API call failed: " . htmlspecialchars($e->getMessage()));
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/transaction-in/{id}",
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
            $payment = $this->mollie->payments->get($id);

            if ($payment) {
                return response()->json($payment);
            }
        } catch (Mollie_API_Exception $e) {
            return response()->json("API call failed: " . htmlspecialchars($e->getMessage()));
        }
    }

    public function checkStates()
    {
        try {
            $payments = $this->mollie->payments->all();

            foreach ($payments as $payment) {
                $paymentCreatedDate = new \DateTime($payment->createdDatetime);
                $stripPaymentDate = $paymentCreatedDate->format('Y-m-d');
                $today = date('Y-m-d');

                if ($today == $stripPaymentDate) {

                    $state = State::where('name', '=', $payment->status)->get();

                    if ($state) {
                        $transaction = DB::table('transaction_ins')
                            ->where('payment_id', $payment->id)
                            ->update(['state_id' => $state[0]->id]);

                        if ($transaction) {
                            return response()->json(['status' => 'success', 'message' => 'Transaction state has been changed']);
                        }
                        return response()->json(['status' => 'failed', 'message' => 'Transaction state has not been changed']);
                    }
                }
            }
        }
        catch (Mollie_API_Exception $e) {
            return response()->json("API call failed: " . htmlspecialchars($e->getMessage()));
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\POST(
     *     path="/transaction-in",
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
        try {
            $payment = $this->mollie->payments->create(array(
                "amount" => $request->input('amount'),
                "description" => $request->input('description'),
                "redirectUrl" => env('RE_DIRECT_URL'),
                "webhookUrl" => env('WEBHOOK_URL'),
            ));

            if ($payment) {
                $check = $this->saveToDatabase($request, $payment->id);
                if ($check) {
                    return response()->json(['status' => 'success', 'message' => 'New transaction has been saved into the database']);
                }
                return response()->json(['status' => 'failed', 'message' => 'Error transaction has not been saved into the database']);

            }
        }
        catch (Mollie_API_Exception $e) {
            return response()->json("API call failed: " . htmlspecialchars($e->getMessage()));
        }
    }

    private function saveToDatabase($request, $id)
    {
        $transaction = new TransactionIn();
        $transaction->account_id = $request->input('account_id');
        $transaction->state_id = $request->input('state_id');
        $transaction->payment_id = $id;
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
     *     path="/transaction-in/{id}",
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
    public function update(Request $request, $id)
    {

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\DELETE(
     *     path="/transaction-in/{id}",
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
            $transaction = TransactionIn::findOrFail($id);
            $transaction->delete();
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['status' => 'failed', 'message' => 'Transaction not found']);
        }
        return response()->json(['status' => 'success', 'message' => 'Transaction has been deleted']);
    }

    public function undoDelete($id)
    {
        try {
            TransactionIn::withTrashed()->findOrFail($id)->restore();
        }
        catch(ModelNotFoundException $e) {
            return response()->json(['status' => 'failed', 'message' => 'Transaction not found']);

        }
        return response()->json(['status' => 'success','Transaction has been restored']);
    }
}