<?php

use Laravel\Lumen\Testing\DatabaseTransactions;

use App\TransactionIn;

class TransactionInTest extends TestCase
{
    protected $baseUrl = '192.168.88.100:8000';

    use DatabaseTransactions;

    /**
     * @test
     */
    public function test_if_we_can_create_a_new_transaction_in_with_the_right_values(){

        $transaction = factory(TransactionIn::class)->make();

        $this->post('transaction/in/create', $transaction->toArray(), ['HTTP_Authorization' => env('ACCESS_TOKEN_TEST')])
            ->seeStatusCode(200)
            ->seeJson();
    }

    /**
     * @test
     */
    public function test_if_we_can_get_all_transactions() {
        
        // $transaction = factory(TransactionIn::class, 2)->create();

        $transaction = new TransactionIn();
        $transaction->account_id = 20003;
        $transaction->state_id = 2;
        $transaction->payment_id = 'tr_5642';
        $transaction->amount = 20;
        $transaction->description = 'test for the unit test';
        $transaction->date = date('Y-m-d');
        $transaction->origin = 'digitalefactuur';
        $transaction->save();

        
        $this->get('transaction/in', ['HTTP_Authorization' => env('ACCESS_TOKEN_TEST')])
            ->seeStatusCode(200)
            ->assertCount(1, array($transaction));
    }

    /**
     * @test
     */
    public function test_if_we_can_get_a_single_transaction_by_id() {

        $transaction = new TransactionIn();
        $transaction->account_id = 20003;
        $transaction->state_id = 2;
        $transaction->payment_id = 'tr_5642';
        $transaction->amount = 20;
        $transaction->description = 'test for the unit test';
        $transaction->date = date('Y-m-d');
        $transaction->origin = 'digitalefactuur';
        $transaction->save();


        $this->get('transaction/in/' . $transaction->id, ['HTTP_Authorization' => env('ACCESS_TOKEN_TEST')])
            ->seeStatusCode(200)
            ->assertCount(1, array($transaction));
    }

    /**
     * @test
     */
    public function test_if_we_can_update_a_single_transaction_by_id() {

        $transaction = new TransactionIn();
        $transaction->account_id = 20003;
        $transaction->state_id = 2;
        $transaction->payment_id = 'tr_5642';
        $transaction->amount = 20;
        $transaction->description = 'test for the unit test';
        $transaction->date = date('Y-m-d');
        $transaction->origin = 'digitalefactuur';
        $transaction->save();

        $transaction->description = 'This is the update test';

        $this->patch('transaction/in/edit/' . $transaction->id, $transaction->toArray(), ['HTTP_Authorization' => env('ACCESS_TOKEN_TEST')])
            ->seeStatusCode(200)
            ->seeJson();

    }

    /**
     * @test
     */
    public function test_if_we_can_delete_a_single_transaction_by_id() {

        $transaction = new TransactionIn();
        $transaction->account_id = 20003;
        $transaction->state_id = 2;
        $transaction->payment_id = 'tr_5642';
        $transaction->amount = 20;
        $transaction->description = 'test for the unit test';
        $transaction->date = date('Y-m-d');
        $transaction->origin = 'digitalefactuur';
        $transaction->save();

        $this->delete('transaction/in/delete/' . $transaction->id, ['HTTP_Authorization' => env('ACCESS_TOKEN_TEST')])
            ->seeStatuscode(400)
            ->seeJson();
    }

}