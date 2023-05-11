<?php namespace App\Modules\Main\Controllers;

use App\Modules\Main\Models\MainModel;
use CodeIgniter\Controller;

class Action extends Controller
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
        return redirect()->to(base_url()); 
    }
}
