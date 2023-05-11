<?php namespace App\Modules\Main\Controllers;

use App\Modules\Main\Models\MainModel;
use App\Core\BaseController;

class Main extends BaseController
{
    private $mainModel;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->mainModel = new MainModel();
    }

    public function index()
	{
		echo "authenticated!";
	}

    function tes(){
        echo json_encode($_POST);
    }
}
