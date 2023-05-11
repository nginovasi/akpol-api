<?php 

namespace App\Modules\Mobile\Controllers;

use App\Modules\Mobile\Models\MobileModel;
use App\Core\BaseController;


class MobileIot extends BaseController
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

    function taruna(){
        $noak = $this->request->getPost('no_ak');

        $taruna = $this->mobileModel->getTaruna($noak);

        if(is_null($taruna)){
            echo json_encode(['success' => false, 'no_ak' => $noak]);
        }else{
            echo json_encode(['success' => true, 'data' => $taruna]);
        }
    }
}