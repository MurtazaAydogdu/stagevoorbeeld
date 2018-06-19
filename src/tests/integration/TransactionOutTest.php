<?php

use Laravel\Lumen\Testing\DatabaseTransactions;

use App\TransactionOut;

class TransactionOutTest extends TestCase
{
    protected $baseUrl = 'artisan:8000';

    use DatabaseTransactions;

    /**
     * @test
     */
    public function testIfWeCanCreateANewTransactionOutWithTheRightValues(){

        $rule = new \StdClass();
        $rule->account_id = 20002;
        $rule->subscription_id = 5;
        $rule->product_id = 3;
        $rule->price = "5.83";
        $rule->quantity = 6;
        $rule->time_period = 'year';
        $rule->priority = 2;
        $rule->origin_name = 'digitalefactuur';
        $rule->is_infinite = 0;

        $data = [
            'description' => 'test', 
            'data' => [
                $rule
            ]
        ];

        $data = json_decode(json_encode($data), true);

        $this->json('POST', 'transaction/out/create', $data, ['HTTP_Authorization'=> env('ACCESS_TOKEN_TEST'), 'Content-Type' => 'application/json'])
            ->seeStatusCode(200)
             ->seeJsonStructure([
                'status',
                'data' => [
                    'id',
                    'account_id', 
                    'subscription_id',
                    'product_id',
                    'amount',
                    'description',
                    'origin',
                    'created_at',
                    'updated_at' 
                ]
            ])
            ->seeJson([
                'account_id' => "20003",
                'subscription_id' => 5,
                'product_id' => 3,
                'amount' => 0,
                'description' => 'test',
                'origin' => 'digitalefactuur',
                'status' => 'success'
            ]);
    }

    /**
     * @test
     */
    public function testIfWeCanGetAllTransactions() {
    
        $transaction = factory(TransactionOut::class, 10)->create();

        $this->get('transaction/out', ['HTTP_Authorization' => env('ACCESS_TOKEN_TEST')])
            ->seeStatusCode(200)
            ->assertCount(10, $transaction->toArray());
    }

      /**
     * @test
     */
    public function testIfWeCanGetASingleTransactionById() {

        $transaction = factory(TransactionOut::class)->create();

        $this->get('transaction/out/' . $transaction->id, ['HTTP_Authorization' => env('ACCESS_TOKEN_TEST')])
            ->seeStatusCode(200)
            ->assertCount(1, array($transaction));
    }

    /**
     * @test
     */
    public function testIfWeCanUpdateASingleTransactionById() {

        $transaction = new TransactionOut();
        $transaction->account_id = 20003;
        $transaction->state_id = 2;
        $transaction->product_id = 1;
        $transaction->subscription_id = 1;
        $transaction->amount = 20;
        $transaction->description = 'This is the update test';
        $transaction->date = date('Y-m-d');
        $transaction->origin = 'digitalefactuur';
        $transaction->save();

        $this->patch('transaction/out/edit/' . $transaction->id, $transaction->toArray(), ['HTTP_Authorization' => env('ACCESS_TOKEN_TEST')])
            ->seeStatusCode(200)
            ->seeJsonStructure([
                'status',
                'data' => [
                    'id',
                    'account_id', 
                    'subscription_id',
                    'product_id',
                    'amount',
                    'description',
                    'origin',
                    'created_at',
                    'updated_at' 
                ]
            ])
            ->seeJson([
                'account_id' => 20003,
                'state_id' => 2,
                'product_id' => 1,
                'subscription_id' => 1,
                'amount' => 20,
                'description' => 'This is the update test',
                'origin' => 'digitalefactuur',
                'status' => 'success'
            ]);
    }

    /**
     * @test
     */
    public function testIfWeCanDeleteASingleTransactionById() {

        $transaction = factory(TransactionOut::class)->create();

        $this->delete('transaction/in/delete/' . $transaction->id, ['HTTP_Authorization' => env('ACCESS_TOKEN_TEST')])
            ->seeStatuscode(200)
            ->seeJsonStructure([
                'status'
            ])
            ->seeJson();
    }
}