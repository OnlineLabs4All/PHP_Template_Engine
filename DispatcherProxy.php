<?php
/**
 * User: Danilo G. Zutin
 * Date: 11.11.15
 * Time: 09:45
 */
namespace ExperimentEngine;

use \Httpful\Request;


class DispatcherProxy
{
    private $base_url;
    private $username;
    private $password;
    private $apikey;

    public function __construct($base_url, $username, $password, $apikey)
    {
        $this->base_url = $base_url;
        $this->username = $username;
        $this->password = $password;
        $this->apikey = $apikey;
    }

    public function searchNextExperiment()
    {
        try{
            $response= Request::get($this->base_url.'/status')
                ->authenticateWith($this->username,$this->password)
                ->addHeader('X-apikey',$this->apikey)
                ->send();
        }
        catch (Exception $e) {
            return array('is_Exception' => true,
                         'error_message' => $e->getMessage());
        }

        if ($response->code != 200){
            return array('is_exception' => true,
                         'error_message' => "Request failed with HTTP Error ".$response->code);
        }

        return array('is_exception' => false,
                     'result' => (array)json_decode($response->body));
    }

    public function dequeueExperiment()
    {
        try{
            $response= Request::get($this->base_url.'/experiment')
                ->authenticateWith($this->username,$this->password)
                ->addHeader('X-apikey',$this->apikey)
                ->send();
        }
        catch (Exception $e) {
            return array('is_Exception' => true,
                'error_message' => $e->getMessage());
        }

        if ($response->code != 200){
            return array('is_exception' => true,
                'error_message' => "Request failed with HTTP Error ".$response->code);
        }

        return array('is_exception' => false,
            'result' => (array)json_decode($response->body));
    }

    public function sendResults($success, $results, $errorReport)
    {
        $httpBody = array('success' => $success,
                          'results' => $results,
                          'errorReport' => $errorReport);
        try{
        $response= Request::post($this->base_url.'/experiment')
            ->authenticateWith($this->username,$this->password)
            ->addHeader('X-apikey',$this->apikey)
            ->body(json_encode($httpBody))
            ->send();
    }
        catch (Exception $e) {
        return array('is_Exception' => true,
            'error_message' => $e->getMessage());
    }

        if ($response->code != 200){
            return array('is_exception' => true,
                'error_message' => "Request failed with HTTP Error ".$response->code);
        }

        return array('is_exception' => false,
            'result' => (array)json_decode($response->body));

    }

}