<?php namespace App\Modules\Mobile\Controllers;

use App\Modules\Mobile\Models\MobileModel;
use App\Core\BaseController;

class MobileAdministrator extends BaseController
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

    // controller -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    function modules_get(){
        $modules = $this->mobileModel->getModules();

        return json_encode($modules);
    }

    function usertypes_get(){
        $userTypes = $this->mobileModel->getUserTypes();

        return json_encode($userTypes);
    }

    // ajax ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    function menu_select_get($module_id)
    {
        $menu = $this->mobileModel->base_get('master_menu', ['module_id' => $module_id, 'menu_id' => NULL])->getResult();

        $option = array_map(function($data){
            return "<option value='".$data->id."'>".$data->menu_name."</option>";
        }, $menu);

        return "<option value=''>Jadikan Menu Utama</option>" . implode("", $option);
    }

    function moduleuser_get()
    {
        $module_user = groupby($this->mobileModel->getModuleUser($this->request->getPost('id')), 'module_id');

        return json_encode($module_user);
    }

    function menu_get($module_id)
    {
        $menus = $this->mobileModel->getParentMenu($module_id);
        $array = array_map(function($menu){
            $x = json_decode(json_encode($menu), true);
            $x['submenu'] = $this->mobileModel->getSubMenu($x['id']);

            return $x;
        }, $menus);

        return json_encode($array);
    }

    function module_select_get(){
        $module = $this->mobileModel->getModules();

        $option = array_map(function($data){
            return "<option value='".$data->id."'>".$data->module_name."</option>";
        }, $module);

        return "<select class='idmodule' name='idmodule[]' required>
                <option value=''>Pilih Modul</option>" .
                implode("", $option) . "<select>";
    }

    // action ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    function manmodul_load()
    {
        $query = "select a.* from master_module a where a.is_deleted = 0";
        $where = ["a.module_url", "a.module_name"];
        $data = json_decode($this->request->getPost('param'), true);
        
        parent::_loadDatatable($query, $where, $data);
    }

    function manmodul_save()
    {   
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_insert('master_module', $data, $userid);
    }

    function manmodul_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);
        parent::_edit('master_module', $data);
    }

    function manmodul_delete()
    {   
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_delete('master_module', $data, $userid);
    }

    function manmenu_load()
    {
        $query = "select a.*, b.module_name, c.menu_name as menu_parent from master_menu a 
        left join master_module b on a.module_id = b.id
        left join master_menu c on a.menu_id = c.id
        where a.is_deleted = 0";
        $where = ["a.menu_url", "a.menu_name", "b.module_name", "c.menu_name"];
        $data = json_decode($this->request->getPost('param'), true);
        
        parent::_loadDatatable($query, $where, $data);
    }

    function manmenu_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        $data['menu_id'] = $this->request->getPost('menu_id') == "" ? null : $this->request->getPost('menu_id');
        parent::_insert('master_menu', $data, $userid);
    }

    function manmenu_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);
        parent::_edit('master_menu', $data);
    }

    function manmenu_delete()
    {   
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_delete('master_menu', $data, $userid);
    }

    function manjenisuser_load()
    {
        $query = "select a.* from master_user_type a where a.is_deleted = 0";
        $where = ["a.user_type_code", "a.user_type_name"];
        $data = json_decode($this->request->getPost('param'), true);
        
        parent::_loadDatatable($query, $where, $data);
    }

    function manjenisuser_save()
    {   
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_insert('master_user_type', $data, $userid);
    }

    function manjenisuser_edit()
    {   
        $data = json_decode($this->request->getPost('param'), true);
        parent::_edit('master_user_type', $data);
    }

    function manjenisuser_delete()
    {   
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_delete('master_user_type', $data, $userid);
    }

    function manuser_load()
    {
        $query = "select a.*, b.user_type_name from master_user a 
        left join master_user_type b on a.user_type_code = b.user_type_code
        where a.is_deleted = 0";
        $where = ["a.user_name", "a.user_username", "a.user_email", "b.user_type_name"];
        $data = json_decode($this->request->getPost('param'), true);
        
        parent::_loadDatatable($query, $where, $data);
    }

    function manuser_save()
    {   
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        $data["user_password"] = md5($data["user_password"]);
        
        parent::_insert('master_user', $data, $userid);
    }

    function manuser_edit()
    {   
        $data = json_decode($this->request->getPost('param'), true);
        parent::_edit('master_user', $data);
    }

    function manuser_delete()
    {   
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_delete('master_user', $data, $userid);
    }


    function manhakakses_save()
    {   
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        $number_menu = count($data['idmenu']);
        $deleted = explode(",", $data['delete']);

        $previlagesData = [];
        for ($i=0; $i < $number_menu; $i++) {
            $previlagesData[] = [
                "id" => $data['id'][$i],
                "menu_id" => $data['idmenu'][$i],
                "v" => unwrap_null(@$data['v'][$i], "0"),
                "i" => unwrap_null(@$data['i'][$i], "0"),
                "d" => unwrap_null(@$data['d'][$i], "0"),
                "e" => unwrap_null(@$data['e'][$i], "0"),
                "o" => unwrap_null(@$data['o'][$i], "0"),
                "user_type_code" => $data['iduser'],
                "created_by" => $userid,
                "created_at" => date("Y-m-d H:i:s")
            ];
        }

        if($this->mobileModel->saveHakAkses($previlagesData, $deleted, $data['iduser'], $userid)){
            echo json_encode(array('success' => true, 'message' => $previlagesData));
        }else{
            echo json_encode(array('success' => false, 'message' => $this->mobileModel->db->error()));
        }
    }
}
