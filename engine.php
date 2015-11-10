<?php
//Template Experiment Engige

require_once('./vendor/nategood/httpful/bootstrap.php');
require_once('config.php');
use \Httpful\Request;

$run = true;
$state = 1;

while ($run == true){

    switch($state){

        case 1: //Search State
            try{
                $response = Request::get($base_url.'/status')
                    ->authenticateWith($username,$password)
                    ->addHeader('X-apikey',$Xapikey)
                    ->expectsJson()
                    ->send();
                echo "STATUS Response: ";
                echo json_encode($response->body);
                echo "\r\n";

                //Check if experiment was found

                if ($response->body->success == true){
                    //If experiment exists, go to state 2 (retrieve experiment Specification)
                    $state = 2;
                }
                sleep(1);
            }
            catch (Exception $e) {
                echo 'Error: '.$e->getMessage();
                echo "\r\n";
                $run = false;
            }
            break;
        case 2:
            try{
                $response = Request::get($base_url.'/experiment')
                    ->authenticateWith($username,$password)
                    ->addHeader('X-apikey',$Xapikey)
                    ->expectsJson()
                    ->send();
                echo "Experiment Specification: ";
                echo json_encode($response->body->expSpecification);
                echo "\r\n";
            }
            catch (Exception $e) {
                echo 'Error: '.$e->getMessage();
                echo "\r\n";
                $run = false;
            }
            $state = 3;
        break;
        case 3:
            $httpBody = array('success' => true,
                              'results' => $expResults,
                              'errorReport' => '');
            try{
                $response = Request::post($base_url.'/experiment')
                    ->authenticateWith($username,$password)
                    ->addHeader('X-apikey',$Xapikey)
                    ->body(json_encode($httpBody))
                    ->expectsJson()
                    ->send();
                echo json_encode($response->body);
                echo "\r\n";

            }
            catch (Exception $e) {
                echo 'Error: '.$e->getMessage();
                echo "\r\n";
                $run = false;
            }
            $state = 1; // return to state 1
        break;
    }

}
?>

