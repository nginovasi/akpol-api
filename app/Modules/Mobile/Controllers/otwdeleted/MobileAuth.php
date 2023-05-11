<?php namespace App\Modules\Mobile\Controllers;

use App\Modules\Mobile\Models\MobileModel;
use App\Core\BaseController;

class MobileAuth extends BaseController
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

    function login(){
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = $this->mobileModel->getUser($username, $password);

        if(!is_null($user)){
            if ($user->is_deleted==0) {
                
                $menu = $this->mobileModel->getMenu($user->user_type_code);

                $response = [
                    "success" => TRUE, 
                    "title" => "Success", 
                    "text" => "Berhasil" ,
                    "user" => $user,
                    "menu" => $menu
                ];
            }else{
                $response = [
                    "success" => false, 
                    "title" => "Error", 
                    "text" => "Pengguna Sudah Tidak Aktif" 
                ];
            }
        }else{
            $response = [
                "success" => false, 
                "title" => "Error", 
                "text" => "Username & Password Salah"
            ];
        }

        echo json_encode($response);
    }
}
