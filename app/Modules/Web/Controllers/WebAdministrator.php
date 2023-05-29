<?php

namespace App\Modules\Web\Controllers;

use App\Modules\Web\Models\WebModel;
use App\Core\BaseController;
use App\Core\BaseModel;

class WebAdministrator extends BaseController
{
    private $webModel;
    private $baseModel;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->webModel = new WebModel();
        $this->baseModel = new BaseModel();
    }

    public function index()
    {
        return redirect()->to(base_url());
    }

    // controller -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    function modules_get()
    {
        $modules = $this->webModel->getModules();

        return json_encode($modules);
    }

    function usertypes_get()
    {
        $userTypes = $this->webModel->getUserTypes();

        return json_encode($userTypes);
    }

    // ajax ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    function menu_select_get($module_id)
    {
        $menu = $this->webModel->base_get('m_menu', ['id_module' => $module_id, 'id_menu' => NULL, 'is_deleted' => 0])->getResult();

        $option = array_map(function ($data) {
            return "<option value='" . $data->id . "'>" . $data->name . "</option>";
        }, $menu);

        return "<option value=''>Jadikan Menu Utama</option>" . implode("", $option);
    }

    function moduleuser_get()
    {
        $module_user = groupby($this->webModel->getModuleUser($this->request->getPost('id')), 'module_id');

        return json_encode($module_user);
    }

    function menu_get($module_id)
    {
        $menus = $this->webModel->getParentMenu($module_id);
        $array = array_map(function ($menu) {
            $x = json_decode(json_encode($menu), true);
            $x['submenu'] = $this->webModel->getSubMenu($x['id']);

            return $x;
        }, $menus);

        return json_encode($array);
    }

    function module_select_get()
    {
        $module = $this->webModel->getModules();

        $option = array_map(function ($data) {
            return "<option value='" . $data->id . "'>" . $data->name . "</option>";
        }, $module);

        return "<select class='idmodule' name='idmodule[]' required>
                <option value=''>Pilih Modul</option>" .
            implode("", $option) . "<select>";
    }

    // action ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    function manmodul_load()
    {
        $query = "select a.* from m_module a where a.is_deleted = 0";
        $where = ["a.url", "a.name"];
        $data = json_decode($this->request->getPost('param'), true);

        parent::_loadDatatable($query, $where, $data);
    }

    function manmodul_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_insert('m_module', $data, $userid);
    }

    function manmodul_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);
        parent::_edit('m_module', $data);
    }

    function manmodul_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_delete('m_module', $data, $userid);
    }

    function manmenu_load()
    {
        $query = "select a.*, b.name as module_name, c.name as menu_parent from m_menu a 
        left join m_module b on a.id_module = b.id
        left join m_menu c on a.id_menu = c.id
        where a.is_deleted = 0";
        $where = ["a.url", "a.name", "b.name", "c.name"];
        $data = json_decode($this->request->getPost('param'), true);

        parent::_loadDatatable($query, $where, $data);
    }

    function manmenu_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        $data['id_menu'] = $data['id_menu'] == "" ? null : $data['id_menu'];
        parent::_insert('m_menu', $data, $userid);
    }

    function manmenu_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);
        parent::_edit('m_menu', $data);
    }

    function manmenu_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_delete('m_menu', $data, $userid);
    }

    function manjenisuser_load()
    {
        $query = "select a.* from m_user_type a where a.is_deleted = 0";
        $where = ["a.code", "a.name"];
        $data = json_decode($this->request->getPost('param'), true);

        parent::_loadDatatable($query, $where, $data);
    }

    function manjenisuser_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_insert('m_user_type', $data, $userid);
    }

    function manjenisuser_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);
        parent::_edit('m_user_type', $data);
    }

    function manjenisuser_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_delete('m_user_type', $data, $userid);
    }

    function manuser_load()
    {
        $query = "select a.*, b.name as user_type_name from m_user a 
        left join m_user_type b on a.type_code = b.code
        where a.is_deleted = 0";
        $where = ["a.name", "a.username", "a.email", "b.name"];
        $data = json_decode($this->request->getPost('param'), true);

        parent::_loadDatatable($query, $where, $data);
    }

    function manuser_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);

        if ($data['type_code'] == 'trn' && $data['id'] != '') {
            $taruna = $this->db->query("SELECT b.tahun_masuk FROM m_user_taruna a 
                left join m_sm_batalyon b on a.id_batalyon = b.id
                where a.id_m_user='" . $data['id'] . "' ")->getRow();

            $data['tahun'] = $taruna->tahun_masuk;
        }

        parent::_ldapAdd($data, function ($result) use ($data, $userid) {
            if ($result["success"]) {
                $data["password"] = md5($data["password"]);
                unset($data['tahun']);
                parent::_insert('m_user', $data, $userid);
            } else {
                echo json_encode($result);
            }
        });
    }

    function manuser_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);
        parent::_edit('m_user', $data);
    }

    function manuser_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_delete('m_user', $data, $userid);
    }


    function manhakakses_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        $number_menu = count($data['idmenu'] ?? []);
        $deleted = explode(",", $data['delete']);

        $previlagesData = [];
        for ($i = 0; $i < $number_menu; $i++) {
            $previlagesData[] = [
                "id" => $data['id'][$i],
                "id_menu" => $data['idmenu'][$i],
                "v" => $data['v'][$i] ?? "0",
                "i" => $data['i'][$i] ?? "0",
                "d" => $data['d'][$i] ?? "0",
                "e" => $data['e'][$i] ?? "0",
                "o" => $data['o'][$i] ?? "0",
                "type_code" => $data['iduser'],
                "created_by" => $userid,
                "created_at" => date("Y-m-d H:i:s")
            ];
        }

        if ($this->webModel->saveHakAkses($previlagesData, $deleted, $data['iduser'], $userid)) {
            echo json_encode(array('success' => true, 'message' => $previlagesData));
        } else {
            echo json_encode(array('success' => false, 'message' => $this->webModel->db->error()));
        }
    }

    function changepass_save()
    {
        $data = json_decode($this->request->getPost('param'), true);
        $edited["userPassword"] = base64_encode($data["new_password"]);

        parent::_ldapModify($data, $edited, function ($result) {
            $result["message"] = !$result["success"] ? "Password Lama Salah" : "";

            echo json_encode($result);
        });
    }

    function changeinfo_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        // var_dump($data['name']);
        // die;
        $file_foto = $this->request->getFile('file_foto');
        $newData = array();

        if ($file_foto != null) {
            if ($file_foto->isValid() && !$file_foto->hasMoved()) {
                $newName = $file_foto->getRandomName();
                if (!is_dir('./public/photo/' . $data['usertype'] . '/' . $data['nik'])) {
                    $file_foto->move('./public/photo/' . $data['usertype'] . '/' . $data['nik'], $newName);
                } else {
                    $image = \Config\Services::image()
                        ->withFile($file_foto)
                        ->resize(480, 640, true, 'height')
                        ->save('./public/photo/' . $data['usertype'] . '/' . $data['nik'] . '/' . $newName, 80);
                }
                $dir = base_url() . '/public/photo/' . $data['usertype'] . '/' . $data['nik'] . '/' . $newName;
                $data['photopath'] = $dir;
                $newData['photopath'] = $dir;
                $response = [
                    'success' => true,
                    'data' => $dir
                ];
            }
        }

        if ($data['usertype'] == 'gdk') {

            // $newData['namagadik'] = $data['name'];
            $newData['id'] = $data['id_user_detail'];
            $this->baseModel->base_update($newData, 'm_user_pendidik', array('id' => $newData['id']));
        } else if ($data['usertype'] == 'trn') {

            // $newData['namataruna'] = $data['name'];
            $newData['id'] = $data['id_user_detail'];
            $this->baseModel->base_update($newData, 'm_user_taruna', array('id' => $newData['id']));
        }


    
        if ($data['usertype'] == 'gdk') {
            $userData = array();
            $userData['name'] = $data['name'];
            $userData['id'] = $data['id'];
            parent::_insert('m_user', $userData, $userid);
        } else if ($data['usertype'] == 'trn') {
            $userData = array();
            $userData['namataruna'] = $data['name']; 
            $userData['telp'] = $data['telp'];
            $userData['id'] = $data['id'];
            parent::_insert('m_user_taruna', $userData, $userid);
        }
    }
}
