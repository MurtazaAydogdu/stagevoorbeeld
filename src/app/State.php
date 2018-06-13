<?php
/**
 * Created by IntelliJ IDEA.
 * User: murtazaaydogdu
 * Date: 27/02/2018
 * Time: 12:17
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class State
 *
 * @package App
 *
 * @SWG\Definition(
 *   definition="New state",
 *   required={"name", "description"},
 *     @SWG\Property(
 *          property="name",
 *          type="string",
 *          description="Name of the state",
 *          example="Open"
 *     ),
 *      @SWG\Property(
 *          property="description",
 *          type="string",
 *          description="Description of the state",
 *          example="The transaction is open"
 *     )
 * )
 *
 */
class State extends Model
{
    use SoftDeletes;

    protected $fillable = ['name','description'];

}