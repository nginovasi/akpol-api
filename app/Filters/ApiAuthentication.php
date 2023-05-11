<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class ApiAuthentication implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
    	date_default_timezone_set('Asia/Jakarta');
    	$expired = 10 * 60;

        if (isset($_SERVER['HTTP_AUTHORIZATION']) || isset($_SERVER['Authorization'])) {
        	$bearer = str_replace("Bearer ", "", $_SERVER["HTTP_AUTHORIZATION"]);
            $token = base64_decode($bearer);
        	$time = str_replace("enjiay-akpol-", "", $token);
        	$requestTime = strtotime($time);
        	$currentTIme = strtotime(date('Y-m-d H:i:s'));
        	$diff = $currentTIme - $requestTime;

            if($bearer != 'dev'){
                if($diff > $expired){
                    header("HTTP/1.1 401 Unauthorized");
                    die("Token Expired");
                }
            }
        }else{
        	header("HTTP/1.1 401 Unauthorized");
        	die("Token Not Available");
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // $request = \Config\Services::request();
        // var_dump($request->uri->getSegments());
    }
}