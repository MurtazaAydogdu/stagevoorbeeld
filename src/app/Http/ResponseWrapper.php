<?php

namespace App\Http;

class ResponseWrapper {


    private $body;

    public function __construct() {
        $this->body = array();
    }
    public function notFound($data) {
        return $this->handleResponse(404, $data);
    }
    
    public function badRequest($data) {
        return $this->handleResponse(400, $data);
    }
    public function serverError($data) {
        return $this->handleResponse(500, $data);
    }
    
    public function ok($data) {
        return $this->handleResponse(200, $data);
    }

    public function reject($data) {
        return $this->handleResponse(403, $data);
    }

    public function notImplemented($data) {
        return $this->handleResponse(501, [
            'message' => 'The requested function is not implemented',
            'code' => 'NotImplemented'
        ]);
    }
    
    private function handleResponse($status_code, $data) {
        $this->body = $this->formatBody($status_code, $data);
        
        return $this->body;
    }
    private function formatBody($status_code, $data = false) {
        
        if($status_code >= 200 && $status_code < 300) {
            $this->body = $this->formatSuccessBody($data);
        } else if($status_code >= 300 && $status_code < 500) {
            $this->body = $this->formatFailBody($data);
        } else {
            $this->body = $this->formatErrorBody($data);
        }
        return $this->body;
    }
    private function formatSuccessBody($data) {

        $this->body['status'] = "success";
        $this->body['data'] = $data;
        
        return $this->body;
    }
    private function formatFailBody($data) {
        $this->body['status'] = "fail";
        $this->body['message'] = isset($data['message']) ? $data['message']: 'Something went wrong on the server';
        
        $this->body['code'] = isset($data['code']) ? $data['code']: 'UnknownError';

        return $this->body;
    }
    private function formatErrorBody($data) {
        $this->body['status'] = "error";
        $this->body['message'] = isset($data['message']) ? $data['message']: 'Something went wrong on the server';
        
        $this->body['code'] = isset($data['code']) ? $data['code']: 'UnknownError';

        unset($data['code']);
    
        if($data) {
            $this->body['data'] = $data;
        }
        return $this->body;
    }
}