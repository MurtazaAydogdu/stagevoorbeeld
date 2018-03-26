<?php

use Laravel\Lumen\Testing\DatabaseTransactions;
use \App\State;

class StateTest extends TestCase
{
    protected $baseUrl = 'localhost:8888/';

    use DatabaseTransactions;

    private $token = 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOjE2LCJyb2xlIjoiREVWIiwiaWF0IjoxNTIxNTUxMTg2LCJleHAiOjM5MzE1NTQ3ODYsImF1ZCI6WyJkaWdpdGFsZWZhY3R1dXIiXSwiaXNzIjoiQXV0aGVudGljYXRpb24gU2VydmVyIn0.CkeCIKPGWIqBRDPVkw91vg9Pw2loHEnwqYxiLYUWkP20D9G68HayeiUKCsI8XMyMiwTlz77ufOmDbgEaLyzBcQ';


    /**
     * @test
     */
    public function test_if_we_can_create_a_new_state_with_the_right_values() {

        $state = factory(State::class)->create();

        $this->post('state/create', $state->toArray(), ['HTTP_Authorization' => $this->token])
            ->seeStatusCode(200)
            ->seeJson();
    }

//    /**
//     * @test
//     */
//    public function test_if_the_validate_returns_an_error_when_sending_name() {
//
//        $state = ['name' => 'Open'];
//
//        $this->post('state', $state, ['HTTP_Authorization' => $this->token] )
//            ->seeStatusCode(422);
//    }
//
//    /**
//     * @test
//     */
//    public function test_if_the_validate_returns_an_error_when_sending_description() {
//
//        $state = ['description' => 'The transaction is paid'];
//
//        $this->post('state', $state, ['HTTP_Authorization' => $this->token])
//            ->seeStatusCode(422);
//    }
//
//    /**
//     * @test
//     */
//    public function test_if_we_can_get_all_states(){
//
//        $this->get('states', ['HTTP_Authorization' => $this->token])
//            ->seeStatusCode(200)
//            ->seeJson();
//    }
//
//    /**
//     * @test
//     */
//    public function test_if_we_can_get_a_single_state() {
//        $state = factory(State::class)->create();
//
//        $this->get('state/' . $state->id, ['HTTP_Authorization' => $this->token])
//            ->seeStatusCode(200)
//            ->seeJson();
//    }
//
//    /**
//     * @test
//     */
//    public function test_if_we_can_update_a_single_state(){
//
//        $state = factory(State::class)->create();
//
//        $state->name = 'Open';
//        $state->description = 'Transaction is open';
//
//        $this->patch('state/' . $state->id, $state->toArray(), ['HTTP_Authorization' => $this->token])
//            ->seeStatusCode(200)
//            ->seeJson();
//    }
//
//    /**
//     * @test
//     */
//    public function test_if_we_can_delete_a_single_state(){
//        $state = factory(State::class)->create();
//
//        $this->delete('state/' . $state->id,['HTTP_Authorization' => $this->token])
//            ->seeStatusCode(401);
////            ->seeJson();
//    }
}