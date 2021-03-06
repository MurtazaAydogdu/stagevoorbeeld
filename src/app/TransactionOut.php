<?php
/**
 * Created by IntelliJ IDEA.
 * User: murtazaaydogdu
 * Date: 16/03/2018
 * Time: 14:03
 */

namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Transaction-In-In
 *
 * @package App
 *
 * @SWG\Definition(
 *   definition="New transaction",
 *   required={"account_id", "state_id", "amount", "description", "date", "origin"},
 *     @SWG\Property(
 *          property="account_id",
 *          type="int",
 *          description="FK to account",
 *          example="1"
 *     ),
 *     @SWG\Property(
 *          property="state_id",
 *          type="int",
 *          description="FK to state",
 *          example="1"
 *     ),
 *     @SWG\Property(
 *          property="amount",
 *          type="int",
 *          description="Amount of the products",
 *          example="1"
 *     ),
 *      @SWG\Property(
 *          property="description",
 *          type="text",
 *          description="Description of the transaction",
 *          example=""
 *     ),
 *      @SWG\Property(
 *          property="date",
 *          type="date",
 *          description="Date of the transaction",
 *          example="01-04-2018"
 *     ),
 *      @SWG\Property(
 *          property="origin",
 *          type="text",
 *          description="Origin of the transaction",
 *          example="DF"
 *     )
 * )
 *
 */
class TransactionOut extends Model
{
    use SoftDeletes;

    protected $fillable = ['account_id', 'state_id', 'product_id', 'subscription_id', 'amount', 'description', 'date', 'origin'];

}