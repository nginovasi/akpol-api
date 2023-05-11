<?php namespace App\Modules\Mobile\Controllers;

use App\Modules\Mobile\Models\MobileModel;
use App\Core\BaseController;

class Mobile extends BaseController
{
    private $mobileModel;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->mobileModel = new MobileModel();
    }

    public function index()
	{
		return redirect()->to(base_url()); 
	}

    function logaction(){
        $action = $this->request->getPost('action');
        $url = $this->request->getPost('url');
        $result = $this->request->getPost('result');
        $userid = $this->request->getPost('userid');
        $ip = $this->request->getPost('ip');

        $this->mobileModel->log_action($action, $url, $result, $userid, $ip);

        return "success";
    }

    public function test()
    {
        return view('App\Modules\Mobile\Views\test'); 
    }

    public function terms(){
        echo "botel";
    }

}
