<?php

namespace App\Http;

class SenderToMessageAdapter {


    public function send($type, $url, $status, $origin, $data) {
        // $data = array("data" => $data);                                                                    
        // $data_string = json_encode($data);                                                                                   
                                                                                                                            
        // $ch = curl_init(env('MESSAGE_ADAPTER') . $url . '?status=' . $status . '&origin=' . $origin);                                                                      
        // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);                                                                     
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);       
        // curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
        //     'Content-Type: application/json'                                                                         
        // ));                                               
                                                                                                                                                                                  
        // $response = curl_exec($ch);


        // return "Test " . $response . " IP " . env('MESSAGE_ADAPTER');

        $client = new \GuzzleHttp\Client([
            'headers' => [
                'Authorization' => 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIzMDAwMyIsImFjY291bnRJZCI6IjIwMDAzIiwicm9sZSI6IlVTRVIiLCJvcmlnaW4iOiJkaWdpdGFsZWZhY3R1dXIiLCJpYXQiOjE1MjgzNjczNDYsImV4cCI6MzMwNzE2NzM2MDAsImF1ZCI6WyJkaWdpdGFsZWZhY3R1dXIiXSwiaXNzIjoiQXV0aGVudGljYXRpb24gU2VydmVyIn0.OvoohCc_nLNIN5Uz3xJoGMcNMtniB07ylv7PPl7ZBwaoz3kpHVx3-m1SSdX9VHiHIyzPs0cXNyibIf81HLAcJYio7gdb0QXqjQsjnDaSIXCFhdFR2EtoRwElLv9fGk-41jb-2SHIZHhcH-8ktSgyuwmn3r8NT5545qMAKXWWs7naijXrY6WSJt7VEc9CotBI0wt5eKp461ZYZ6RAoKvqyWwFj4mP-uKCCbPf14eWP2Tpi4Cjybu77hCJMO8_sU5bI9vA5ajVR2B1ZTYmIbaIl0XRxjHsbMVHah426y3l89AAD4U3-eSMNEtP-s25Xnf9KhlG1-k48xDgg3FZJ9Os0FdY9q4nQy4euN4n7Xxj0xevBz3CI8A6FoMo54YWAGW4ei-o_Qw6vZ_jYl0uAE8aNuzHejUQsqekEhsT19C9qRdWk3Mv_VHRZT31w0VT6JoYUDkHfQxlU31YDZ7rO6V2569CdDtYc3Ac5eEevZrSr5KcOtFvJLY1zcjTCnR7a-T5FrtxuamNmes7HPUJ4CdeM4A6C3BfYdg5-ImbcT5Uzn8IKeRLUAtTyk2Dnf8qpjC0kQmKpntoMp6LspfRPIjo6FH_rmlUV9Vo47CBWgQnfjoEoNGST4QdO8tpLU6hp7TFpTOzk5bI7zPMuoLbyPPPt4Z5NjGIEfFsHd-QtpGzTis'
            ],
        ]);
        
        $endpoint = env('MESSAGE_ADAPTER') . $url . '?status=' . $status . '&origin=' . $origin;

        $response = $client->request('POST', $endpoint, ['json' => $data]);


        return $response->response->getContent();
    }
}