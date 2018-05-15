<?php

namespace App\Repositories;

use App\Interfaces\TransactionInInterface;
use App\TransactionIn;
use Illuminate\Support\Facades\Validator;
use App\Http\ResponseWrapper;

class TransactionInRepository implements TransactionInInterface {

    private $reponseWrapper;

    public function __construct(){
        $this->responseWrapper = new ResponseWrapper();
    }

    public function get($id) {
        return TransactionIn::where('account_id', $id)->get();
    }

    public function create($amount, $description) {
        try {
            $transaction = new TransactionIn();
            $transaction->account_id = ACCOUNT_ID;
            $transaction->state_id = 2;
            $transaction->amount = $amount;
            $transaction->description = $description;
            $transaction->date = date('Y-m-d');
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


}