<?php

namespace App;

class Request {
    private $ch;
    private $body;
    private $status_code;

    public function __construct() {    
        $this->ch = curl_init();
    }

    public function call($method, $url, $headers, $payload) {

        curl_setopt_array($this->ch, [
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $url,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => json_encode($payload)
        ]);
        
        $response = curl_exec($this->ch);
        
        if (curl_errno($this->ch)) {
            echo curl_error($this->ch);
        }

        $this->status_code = curl_getinfo($this->ch, CURLINFO_RESPONSE_CODE);

        curl_close($this->ch);

        $data = json_decode($response, true);
        $this->body = $data;

        return $this;
    }
    
    public function get($url, $headers = [], $payload = []) {
        
        return $this->call("GET", $url, $headers, $payload);
    }

    public function post($url, $headers = [], $payload = []) {
        
        return $this->call("POST", $url, $headers, $payload);
    }

    public function patch($url, $headers = [], $payload = []) {
        
        return $this->call("PATCH", $url, $headers, $payload);
    }

    public function delete($url, $headers = [], $payload = []) {
        
        return $this->call("DELETE", $url, $headers, $payload);
    }

    public function getBody() {
        
        return $this->body;
    }
   
    public function getStatusCode() {
        
        return $this->status_code;
    }
   
}