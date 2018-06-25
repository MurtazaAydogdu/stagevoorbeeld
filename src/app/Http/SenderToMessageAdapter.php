<?php

namespace App\Http;

class SenderToMessageAdapter {


    public function send($type, $url, $status, $origin, $data) {                                                              
                                                                                                                            
        $ch = curl_init(env('MESSAGE_ADAPTER') . $url . '?status=' . $status . '&origin=' . $origin);                                                                      
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);                                                                     
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));                                                                  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);       
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
            'Content-Type: application/json'                                                                         
        ));                                               
                                                                                                                                                                                  
        $response = curl_exec($ch);
        $error_msg = '';
        if (curl_error($ch)) {
            $error_msg = curl_error($ch);
        }

        var_dump($error_msg);

        return "Test " . $response . " IP " . env('MESSAGE_ADAPTER');
    }
}