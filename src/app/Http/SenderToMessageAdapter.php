<?php

namespace App\Http;

class SenderToMessageAdapter {


    public function send($type, $url, $status, $origin, $data) {
       try {
            $data = array("data" => $data);                                                                    
            $data_string = json_encode($data);                                                                                   
                                                                                                                                
            $ch = curl_init(env('MESSAGE_ADAPTER') . $url . '?status=' . $status . '&origin=' . $origin);                                                                      
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);                                                                     
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
                'Content-Type: application/json'                                                                         
            ));                                                                                                                   
                                                                                                                                
            $result = curl_exec($ch);
       }

       catch(\Exception $e) {
        $myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
        $txt = "John Doe\n";
        fwrite($myfile, $txt);
        $txt = $e->getMessage();
        fwrite($myfile, $txt);
        fclose($myfile);
       }
    }
}