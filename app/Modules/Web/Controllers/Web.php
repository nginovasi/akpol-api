<?php namespace App\Modules\Web\Controllers;

use App\Modules\Web\Models\WebModel;
use App\Core\BaseController;

class Web extends BaseController
{
    private $webModel;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->webModel = new WebModel();
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
        $param = $this->request->getPost('param');
        $user_agent = $this->request->getPost('user_agent');

        $this->webModel->log_action($action, $url, $result, $userid, $ip, $param, $user_agent);

        return "success";
    }

    public function test()
    {
        return view('App\Modules\Web\Views\test'); 
    }

    function tes(){
        echo json_encode($_POST);
    }
}
