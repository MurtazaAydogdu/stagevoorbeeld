<?php

namespace App\Http;

class SenderToMessageAdapter {


    public function send($type, $url, $status, $origin, $data) {
        $data = array("data" => $data);                                                                    
        $data_string = json_encode($data);                                                                                   
                                                                                                                            
        $ch = curl_init(env('MESSAGE_ADAPTER') . $url . '?status=' . $status . '&origin=' . $origin);                                                                      
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);                                                                     
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);       
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
            'Content-Type: application/json'                                                                         
        ));                                               
                                                                                                                                                                                  
        $response = curl_exec($ch);


        return "Test " . $response . " IP " . env('MESSAGE_ADAPTER');
    }
}