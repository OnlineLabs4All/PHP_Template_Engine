<?php
//Template Experiment Engige

require_once('./vendor/nategood/httpful/bootstrap.php');
use \Httpful\Request;

$expSpec = "<?xml version='1.0' encoding='utf-8' standalone='no' ?> <!DOCTYPE experimentResult SYSTEM 'http://exp01.cti.ac.at/elvis/xml/experimentResult.dtd'> <experimentResult lab='MIT NI-ELVIS Weblab' specversion='0.1'><datavector name='TIME'>0.000000 0.010000 0.020000 0.030000 0.040000 0.050000 0.060000 0.070000 0.080000 0.090000 0.100000 0.110000 0.120000 0.130000 0.140000 0.150000 0.160000 0.170000 0.180000 0.190000 0.200000 0.210000 0.220000 0.230000 0.240000 0.250000 0.260000 0.270000 0.280000 0.290000 0.300000 0.310000 0.320000 0.330000 0.340000 0.350000 0.360000 0.370000 0.380000 0.390000 0.400000 0.410000 0.420000 0.430000 0.440000 0.450000 0.460000 0.470000 0.480000 0.490000 0.500000</datavector><datavector name='VOUT'>-1.835922 1.929340 4.951962 5.276399 4.862557 1.780822 -1.986693 -4.990854 -5.280159 -4.843624 -1.744429 2.020191 5.007216 5.276399 4.803276 1.689488 -2.076253 -5.041917 -5.280159 -4.784185 -1.653258 2.109753 5.061181 5.276399 4.742545 1.596544 -2.165009 -5.098618 -5.280159 -4.723457 -1.562570 2.198349 5.113052 5.276399 4.682619 1.506821 -2.253764 -5.150487 -5.280159 -4.662245 -1.467694 2.288072 5.163152 5.276399 4.620761 1.410655 -2.343646 -5.199779 -5.280159 -4.601356 -1.374912</datavector><datavector name='VIN'>0.604925 -0.633139 -1.627485 -2.000546 -1.600424 -0.585942 0.651639 1.639069 2.001666 1.595899 0.573192 -0.663905 -1.645526 -1.997646 -1.582705 -0.556626 0.683372 1.656144 1.999572 1.577214 0.544198 -0.694510 -1.664533 -1.995069 -1.563697 -0.526665 0.713011 1.676440 1.997317 1.555307 0.516492 -0.724632 -1.682413 -1.993458 -1.541952 -0.497832 0.740878 1.695448 1.995062 1.533560 0.486209 -0.752016 -1.699971 -1.992170 -1.520206 -0.467065 0.769389 1.712040 1.994740 1.512298 0.454637</datavector></experimentResult>";

$base_url = 'http://dispatcher.onlinelabs4all.org/apis/engine';
$username = 'htl';
$password = 'fhiscool';
$Xapikey = '892c358f3dd0f2d33c1398530f32fe24';


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
                              'results' => $expSpec,
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

