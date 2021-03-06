<?php

use Laravel\Lumen\Testing\DatabaseTransactions;

use App\TransactionIn;

class TransactionInTest extends TestCase
{
    protected $baseUrl = 'artisan:8000';

    use DatabaseTransactions;

    /**
     * @test
     */
    public function testIfWeCanCreateANewTransactionInWithTheRightValues(){

        $transaction = factory(TransactionIn::class)->make();

        $this->post('transaction/in/create', $transaction->toArray(), ['HTTP_Authorization' => env('ACCESS_TOKEN_TEST')])
            ->seeStatusCode(200)
            ->seeJson();

    }

    /**
     * @test
     */
    public function testIfWeCanGetAllTransactions() {
        
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
    public function testIfWeCanGetASingleTransactionById() {

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
    public function testIfWeCanUpdateASingleTransactionById() {

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
    public function testIfWeCanDeleteASingleTransactionById() {

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
            ->seeStatuscode(200)
            ->seeJson();
    }

}