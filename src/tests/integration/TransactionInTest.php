<?php
use App\TransactionIn;
use Laravel\Lumen\Testing\DatabaseTransactions;

/**
 * Created by IntelliJ IDEA.
 * User: murtazaaydogdu
 * Date: 23/03/2018
 * Time: 13:32
 */
class TransactionInTest extends TestCase
{
    protected $baseUrl = 'localhost:8888/';

    use DatabaseTransactions;

    private $token = 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOjE2LCJyb2xlIjoiREVWIiwiaWF0IjoxNTIxNTUxMTg2LCJleHAiOjM5MzE1NTQ3ODYsImF1ZCI6WyJkaWdpdGFsZWZhY3R1dXIiXSwiaXNzIjoiQXV0aGVudGljYXRpb24gU2VydmVyIn0.CkeCIKPGWIqBRDPVkw91vg9Pw2loHEnwqYxiLYUWkP20D9G68HayeiUKCsI8XMyMiwTlz77ufOmDbgEaLyzBcQ';


    /**
     * @test
     */
    public function test_if_we_can_create_a_new_transaction_in_with_the_right_values(){

        $transaction = factory(TransactionIn::class)->create();

        $this->post('transaction/in/create', $transaction->toArray(), ['HTTP_Authorization' => $this->token])
            ->seeStatusCode(200)
            ->seeJson();
    }

    /**
     * @test
     */
    public function test_if_we_can_get_all_transactions() {

        $this->get('transaction/in', ['HTTP_Authorization' => $this->token])
            ->seeStatusCode(200)
            ->seeJson();
    }

    /**
     * @test
     */
    public function test_if_we_can_get_a_single_transaction_by_id() {
        $transaction = factory(TransactionIn::class)->create();

        $this->get('transaction/in/' . $transaction->id, ['HTTP_Authorization' => $this->token])
            ->seeStatusCode(200)
            ->seeJson();
    }

    /**
     * @test
     */
    public function test_if_we_can_update_a_single_transaction_by_id() {
        $transaction = factory(TransactionIn::class)->create();

        $transaction->description = 'This is the update test';

        $this->patch('transaction/in/edit/' . $transaction->id, $transaction->toArray(), ['HTTP_Authorization' => $this->token])
            ->seeStatusCode(200)
            ->seeJson();

    }

    /**
     * @test
     */
    public function test_if_we_can_delete_a_single_transaction_by_id() {
        
        $transaction = factory(TransactionIn::class)->create();

        $this->delete('transaction/in/delete/' . $transaction->id, ['HTTP_Authorization' => $this->token])
            ->seeStatuscode(200)
            ->seeJson();
    }

}