<?php
/**
 * Created by IntelliJ IDEA.
 * User: murtazaaydogdu
 * Date: 13/03/2018
 * Time: 11:28
 */

namespace App\Http\Controllers;

use App\TransactionOut;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;


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
    public function __construct(){
        $this->middleware('auth');
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
        $transaction = TransactionOut::where('origin', ORIGIN_NAME)->get();

        if ($transaction) {
            return response()->json(['status'=> 'success', 'transaction' => $transaction]);
        }
        return response()->json(['status'=> 'success','message' => 'No transactions found']);
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
            $transaction = TransactionOut::where('origin', ORIGIN_NAME)->findOrFail($id);
    
            if ($transaction) {
                return response()->json(['status'=> 'success', 'transaction' => $transaction]);
            }
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['status'=>'failed', 'message'=>'No transaction found']);
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
                return response()->json(['status'=> 'success', 'message' => 'Transaction has been deleted']);
            }
        }
        catch(ModelNotFoundException $e) {
            return response()->json(['status'=> 'failed', 'message' => 'No transaction found']);
        }
    }

    public function restore($id) {
        try {
            $restored = TransactionOut::withTrashed()->findOrFail($id)->restore();

            if ($restored) {
                return response()->json(['status' => 'success', 'message' => 'Transaction has been restored']);
            }
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['status' => 'failed', 'message' => 'No transaction found']);
        }
    }
}