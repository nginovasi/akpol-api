<?php namespace App\Controllers;

// use App\Modules\Mobile\Models\MobileModel;
use CodeIgniter\Controller;

class MobilePublic extends Controller
{
    // public function __construct()
    // {
    //     $this->mobileModel = new MobileModel();
    // }
    
    public function test()
    {
        return view('App\Modules\Mobile\Views\test'); 
    }

    public function terms(){
        echo "botel";
    }

}