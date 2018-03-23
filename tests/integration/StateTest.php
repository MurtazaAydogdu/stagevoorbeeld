<?php

use Laravel\Lumen\Testing\DatabaseTransactions;
use \App\State;

class StateTest extends TestCase
{
    protected $baseUrl = 'http://transaction_api.test';

    use DatabaseTransactions;

    private $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJPbmxpbmUgSldUIEJ1aWxkZXIiLCJpYXQiOjE1MjExMDg4MDIsImV4cCI6MTU1MjY0NDgwMiwiYXVkIjoid3d3LmV4YW1wbGUuY29tIiwic3ViIjoianJvY2tldEBleGFtcGxlLmNvbSIsIkFjY291bnRfSUQiOiIxIiwiU291cmNlIjoiREYifQ.TckP4zbdclTfzMDeDuN1hWQUjiKKipHZi0MVQNSDeEE';


    /**
     * @test
     */
    public function test_if_we_can_create_a_new_state_with_the_right_values() {

        $state = factory(State::class)->create();

        $this->post('state', $state->toArray(), ['HTTP_Authorization' => $this->token])
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