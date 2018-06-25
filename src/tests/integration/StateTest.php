<?php

use Laravel\Lumen\Testing\DatabaseTransactions;
use App\State;


class StateTest extends TestCase
{
    protected $baseUrl = 'artisan:8000';

    use DatabaseTransactions;
    /**
     * @test 
     */
    public function testIfWeCanCreateANewStateWithTheRightValues() {

        $state = factory(State::class)->create();

        $this->post('state/create', $state->toArray(), ['HTTP_Authorization' => env('ACCESS_TOKEN_TEST')])
            ->seeStatusCode(200)
            ->seeJson();
    }

   /**
    * @test
    */
   public function testIfWeCanGetAllStates(){

        $state = factory(State::class,1)->create();


        $this->get('states', ['HTTP_Authorization' => env('ACCESS_TOKEN_TEST')])
            ->seeStatusCode(200)
            ->assertCount(1, array($state));
        }

   /**
    * @test
    */
   public function testIfWeCanGetASingleState() {
       $state = factory(State::class)->create();

       $this->get('state/' . $state->id, ['HTTP_Authorization' => env('ACCESS_TOKEN_TEST')])
           ->seeStatusCode(200)
           ->assertCount(1, array($state));
        }

   /**
    * @test
    */
   public function testIfWeCanUpdateASingleState(){

       $state = factory(State::class)->create();

       $state->name = 'Open';
       $state->description = 'Transaction is open';

       $this->patch('state/edit/' . $state->id, $state->toArray(), ['HTTP_Authorization' => env('ACCESS_TOKEN_TEST')])
           ->seeStatusCode(200)
           ->seeJson();
   }

   /**
    * @test
    */
    public function testIfWeCanDeleteASingleState(){

       $state = factory(State::class)->create();
       
       $this->delete('state/delete/' . $state->id, ['HTTP_Authorization' => env('ACCESS_TOKEN_TEST')])
            ->seeStatusCode(200)
            ->seeJson();
        
    }
}