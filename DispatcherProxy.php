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
            $is_exception = false;
            $response= Request::get($this->base_url.'/status')
                ->authenticateWith($this->username,$this->password)
                ->addHeader('X-apikey',$this->apikey)
                ->expectsJson()
                ->send();
        }
        catch (Exception $e) {
            $is_exception = true;
            $response = array('error_message' => $e->getMessage());
        }
    return array('is_exception' => $is_exception,
                 'result' => $response->body);
    }

    public function dequeueExperiment()
    {
        try{
            $is_exception = false;
            $response = Request::get($this->base_url.'/experiment')
                ->authenticateWith($this->username,$this->password)
                ->addHeader('X-apikey',$this->apikey)
                ->expectsJson()
                ->send();
        }
        catch (Exception $e) {
            $is_exception = true;
            $response = array('error_message' => $e->getMessage());
        }

        return array('is_exception' => $is_exception,
                     'result' => $response->body);
    }

    public function sendResults($success, $results, $errorReport)
    {
        $httpBody = array('success' => $success,
                          'results' => $results,
                          'errorReport' => $errorReport);

        try{
            $is_exception = false;
            $response = Request::post($this->base_url.'/experiment')
                ->authenticateWith($this->username,$this->password)
                ->addHeader('X-apikey',$this->apikey)
                ->body(json_encode($httpBody))
                ->expectsJson()
                ->send();
        }
        catch (Exception $e) {
            $is_exception = true;
            $response = array('error_message' => $e->getMessage());
        }
        return array('is_exception' => $is_exception,
                     'result' => $response->body);
    }

}