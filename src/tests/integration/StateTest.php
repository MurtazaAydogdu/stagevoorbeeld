<?php

use Laravel\Lumen\Testing\DatabaseTransactions;
use \App\State;

class StateTest extends TestCase
{
    protected $baseUrl = 'localhost:8888/';

    use DatabaseTransactions;

    private $token = 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIzMDAwMyIsImFjY291bnRJZCI6IjIwMDAzIiwicm9sZSI6IlVTRVIiLCJvcmlnaW4iOiJkaWdpdGFsZWZhY3R1dXIiLCJpYXQiOjE1MjgyNzIxNzIsImV4cCI6MTUyODI3NTc3MiwiYXVkIjpbImRpZ2l0YWxlZmFjdHV1ciJdLCJpc3MiOiJBdXRoZW50aWNhdGlvbiBTZXJ2ZXIifQ.NQ0qvA1BEJZ5_iDtts0qnMWGwhVeEVOgowJZm9sSlL7FnDq-Lxf8-80MGRwEaH00F_nC5e-G4Rm_JD7SB5I7hv_YWohYDD7598KzWOV0P25w3nNuAxm-YO_Yafw4R66eoTEfYtOzptsSEJ7X8yrE5gSrT4MxgdHkevMihycpuH7GxQ3TJWk-jy8E8XXMiGSzeHDjq5Yh2IdU1LAsWqKeRfDm-P_r7BH1R-inos4yP05svx3jGPkROg55cYvQgE2Qrb1s6sxQAxgxZmxTOv7d_D6TYA8Jxq72JxEg6VuzgXRIvkGjpbRQcj7FBmiCep4FAaiyhiborvsC9IEMbd_FgNJUBiwTyWVj4NjYqQDYITP88cjPGj1TMTn3LTXdBNK0dsVbEHtDcMpoza3GpqIyz79UpMAkD0vkgx6Nm185iIwhb_pYyZlxbUIgM7Dz9HcCNVSoRE5HGrbVD3r9ba0FHDY_8CuMv98TREA8z6k7UY615nvkm339psLVJwxQaRysYqjXlEXOMETKG1ob5wHaLnjsTrcHaOvTk8s3b3tdZ6gELq3EkMM6UAPqqy23SyzIMJi_kxXol2Z9QObufrwY0Px6suivOk2fgZm4HCIj96_CLsXQit4h2-FuXDGtkTcspYU8MP13TynSfYcRiXoKGMtIMOYHyIbyPLALCKcbu4w';


    /**
     * @test 
     */
    public function test_if_we_can_create_a_new_state_with_the_right_values() {

        $state = factory(State::class)->create();

        $this->post('state/create', $state->toArray(), ['HTTP_Authorization' => $this->token])
            ->seeStatusCode(200)
            ->seeJson();
    }

   /**
    * @test
    */
   public function test_if_we_can_get_all_states(){

       $this->get('states', ['HTTP_Authorization' => $this->token])
           ->seeStatusCode(200)
           ->seeJson();
   }

   /**
    * @test
    */
   public function test_if_we_can_get_a_single_state() {
       $state = factory(State::class)->create();

       $this->get('state/' . $state->id, ['HTTP_Authorization' => $this->token])
           ->seeStatusCode(200)
           ->seeJson();
   }

   /**
    * @test
    */
   public function test_if_we_can_update_a_single_state(){

       $state = factory(State::class)->create();

       $state->name = 'Open';
       $state->description = 'Transaction is open';

       $this->patch('state/edit/' . $state->id, $state->toArray(), ['HTTP_Authorization' => $this->token])
           ->seeStatusCode(200)
           ->seeJson();
   }

   /**
    * @test
    */
   public function test_if_we_can_delete_a_single_state(){
       $state = factory(State::class)->create();

       $this->delete('state/delete/' . $state->id,['HTTP_Authorization' => $this->token])
           ->seeStatusCode(401);
//            ->seeJson();
   }

   /**
    * @test
    */
   public function test_if_we_can_restore_a_single_state() {

        //only for this test
        if ((\App::environment() == 'testing') && array_key_exists("HTTP_Authorization",  LRequest::server())) {
            $headers['Authorization'] = LRequest::server()["HTTP_Authorization"];
        }

        $state = factory(State::class)->create();

        $delete = $this->delete('state/delete/' . $state->id, ['HTTP_Authorization' => $this->token]);

        if ($delete) {
            $this->delete('state/restore/' . $state->id, ['HTTP_Authorization' => $this->token])
                ->seeStatusCode(200)
                ->seeJson();
        }

   }
}