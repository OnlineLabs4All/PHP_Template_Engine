<?php
//Template Experiment Engige

namespace ExperimentEngine;

require_once('./vendor/nategood/httpful/bootstrap.php');
require_once('DispatcherProxy.php');
require_once('config.php');


if (isset($argv[1])){
    $Xapikey = $argv[1];
}
else{
    $Xapikey = APIKEY;
}

$run = true;
$state = SEARCH;

echo "\n Engine started with key: ".$Xapikey."\n";
$engineProxy = new DispatcherProxy(BASE_URL, USERNAME, PASSWORD, $Xapikey);

while ($run == true){

    switch($state){
        case SEARCH: //Search State
            $response = $engineProxy->searchNextExperiment();

            if ($response['is_exception'] == true){
                waitOneSecondSearch("SEARCH: ".$response['error_message']);
            }
            else{
                if ($response['result']['success'] == true){
                    $state = DEQUEUES;
                }
                else{
                    waitOneSecondSearch($response['result']['message']);
                }
            }
            break;

        case DEQUEUES:
            //Dequeues the experiment and retrieves the experiment specification
            $response = $engineProxy->dequeueExperiment();

            if ($response['is_exception'] == true){
                logMessage("DEQUEUES: ".$response['error_message']);
            }
            else{
                if ($response['result']['success'] == true){
                    //retrieve Experiment specification
                    $expSpec = $response['result']['expSpecification'];
                    echo "Experiment Specification: ";
                    echo json_encode($expSpec);
                }
            }
            $state = RUN; // go to next state "RUN"
        break;

        case RUN:

            // Method to execute the experiment should be called here
            // =========================================================
            $success = true;
            $results = EXP_RESULTS; //Return results from a constant
            $errorReport = '';
            // =========================================================

            $response = $engineProxy->sendResults($success, $results, $errorReport);

            if ($response['is_exception'] == true){
                logMessage("RUN: ".$response['error_message']);
            }
            else{
                if ($response['result']['success'] == true){
                    echo "\n";
                    echo json_encode($response['result']);
                    echo "\n";
                }
            }
            // return to state "SEARCH"
            $state = SEARCH;
        break;
    }
}


function waitOneSecondSearch($message)
{
    echo $message."\r";
    usleep(1000000);
}

function logMessage($message)
{
    echo $message."\n";
}

?>
