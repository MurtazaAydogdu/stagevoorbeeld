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

        $transaction = factory(TransactionIn::class)->create();

        $this->post('transaction/in/create', $transaction->toArray(), ['HTTP_Authorization' => env('ACCESS_TOKEN_TEST')])
            ->seeStatusCode(200)
            ->seeJson();
    }

    /**
     * @test
     */
    public function test_if_we_can_get_all_transactions() {

        $transaction = factory(TransactionIn::class)->create();

        $this->get('transaction/in', ['HTTP_Authorization' => env('ACCESS_TOKEN_TEST')])
            ->seeStatusCode(200)
            ->seeJson();
    }

    /**
     * @test
     */
    // public function test_if_we_can_get_a_single_transaction_by_id() {
    //     $transaction = factory(TransactionIn::class)->create();


    //     $this->get('transaction/in/' . $transaction->id, ['HTTP_Authorization' => env('ACCESS_TOKEN_TEST')])
    //         ->seeStatusCode(200)
    //         ->seeJson();
    // }

    // /**
    //  * @test
    //  */
    // public function test_if_we_can_update_a_single_transaction_by_id() {
    //     $transaction = factory(TransactionIn::class)->create();

    //     $transaction->description = 'This is the update test';

    //     $this->patch('transaction/in/edit/' . $transaction->id, $transaction->toArray(), ['HTTP_Authorization' => env('ACCESS_TOKEN_TEST')])
    //         ->seeStatusCode(200)
    //         ->seeJson();

    // }

    // /**
    //  * @test
    //  */
    // public function test_if_we_can_delete_a_single_transaction_by_id() {
        
    //     $transaction = factory(TransactionIn::class)->create();

    //     $this->delete('transaction/in/delete/' . $transaction->id, ['HTTP_Authorization' => env('ACCESS_TOKEN_TEST')])
    //         ->seeStatuscode(200)
    //         ->seeJson();
    // }

}