<?php

namespace App\Modules\Web\Models;

use App\Core\BaseModel;

class WebModel extends BaseModel
{

    function __construct()
    {
        parent::__construct();
    }

    public function getUser($cn, $samaccountname, $username)
    {
        return $this->db->query('select * from m_user where username = "' . $cn . '" or username = "' . $username . '" or username = "' . $samaccountname . '"')->getRow();
    }

    public function getUserWithPassword($username, $password)
    {
        return $this->db->query('select * from m_user where username = ? and password = md5(?)', array($username, $password))->getRow();
    }

    public function getUserType($usertype)
    {
        return $this->db->query('select * from m_user_type where code = "' . $usertype . '"')->getRow();
    }

    public function getMenu($userType, $userType_add, $type_code_pgh)
    {
        return $this->db->query('SELECT a.id , max(a.v) as v , max(a.i) as i , max(a.e) as e ,max(a.d) as d , max(a.o) as o , a.id_menu , a.`type_code`, b.url as menu_url, b.name as menu_name, c.url as module_url, c.name as module_name, d.name as menu_parent from m_user_privileges a
                                inner join m_menu b on a.id_menu = b.id and b.is_deleted = 0
                                inner join m_module c on b.id_module = c.id and c.is_deleted = 0
                                left join m_menu d on b.id_menu = d.id and d.is_deleted = 0
                                where a.type_code in ("' . $userType . '", "' . $userType_add . '", "' . $type_code_pgh . '") and a.v = 1 and a.is_deleted = 0 
            group by a.id_menu')->getResult();
    }

    public function getModules()
    {
        return $this->db->query('select a.* from m_module a where a.is_deleted = 0')->getResult();
    }

    public function getUserTypes()
    {
        return $this->db->query('select a.* from m_user_type a where a.is_deleted = 0')->getResult();
    }

    public function getModuleUser($user_code)
    {
        return $this->db->query('select a.*, b.name as menu_name, b.url as menu_url, c.id as module_id, 
            c.name as module_name from m_user_privileges a 
            inner join m_menu b on a.id_menu = b.id and b.is_deleted = 0
            inner join m_module c on b.id_module = c.id and c.is_deleted = 0
            where a.type_code = "' . $user_code . '" and a.is_deleted = 0
            ')->getResult();
    }

    public function getParentMenu($module_id)
    {
        return $this->db->query('select * from m_menu where id_module = "' . $module_id . '" and id_menu is null and is_deleted = 0')->getResult();
    }

    public function getSubMenu($menu_id)
    {
        return $this->db->query('select * from m_menu where id_menu = "' . $menu_id . '" and is_deleted = 0')->getResult();
    }

    public function deleteByModuleAndUserType($id, $user_type)
    {
        return $this->db->query('delete a.* from m_user_privileges a 
            inner join m_menu b on a.id_menu = b.id
            inner join m_module c on b.id_module = c.id
            where a.type_code = "' . $user_type . '" and c.id = "' . $id . '" and a.is_deleted = 1');
    }

    public function deleteMenuByModuleAndUserType($id, $user_type, $userid)
    {
        return $this->db->query('update m_user_privileges a 
            inner join m_menu b on a.id_menu = b.id
            inner join m_module c on b.id_module = c.id
            set a.is_deleted = 1, a.last_edited_at = "' . date("Y-m-d H:i:s") . '", a.last_edited_by = "' . $userid . '"
            where a.type_code = "' . $user_type . '" and c.id = "' . $id . '"');
    }

    public function saveHakAkses($previleges, $deleted, $user_type, $userid)
    {
        $this->db->transBegin();

        foreach ($deleted as $deleted_id) {
            $this->deleteByModuleAndUserType($deleted_id, $user_type);
            $this->deleteMenuByModuleAndUserType($deleted_id, $user_type, $userid);
        }

        foreach ($previleges as $previlege) {

            $previlagesUpdate = [
                "v = '" . $previlege["v"] . "'",
                "i = '" . $previlege["i"] . "'",
                "d = '" . $previlege["d"] . "'",
                "e = '" . $previlege["e"] . "'",
                "o = '" . $previlege["o"] . "'",
                "last_edited_by = '" . $userid . "'",
                "last_edited_at = '" . date("Y-m-d H:i:s") . "'"
            ];

            $query = $this->string_insert($previlege, 'm_user_privileges') . ' ON DUPLICATE KEY UPDATE ' . implode(", ", $previlagesUpdate);
            $this->db->query($query);
        }

        if ($this->db->transStatus() === FALSE) {
            $this->db->transRollback();
            $this->db->transComplete();
            return false;
        } else {
            $this->db->transCommit();
            $this->db->transComplete();
            return true;
        }
    }

    public function getUserDetail($type_code, $id)
    {
        if ($type_code == 'gdk') {
            return $this->db->query('SELECT a.*, a.namagadik as nama, if(a.id_gender=1, "Laki - Laki" , "Perempuan") as nama_gender,
                    f.suku as nama_suku, e.satker as nama_satker,
                    g.agama as nama_agama, i.nama as nama_kabkota_lhr,
                    j.nama as nama_prov_ktp, k.nama as nama_kota_kab_ktp, l.nama as nama_kec_ktp, m.nama as nama_kel_ktp from m_user_pendidik a 
                    left join m_satker e on a.id_satker = e.id
                    left join m_suku f on a.id_suku = f.id
                    left join m_agama g on a.id_agama = g.id
                    left join m_lokasi_lahir i on a.id_kabkota_lhr = i.kdkabkota
                    left join m_lokasi_nik_ind j on a.id_prov_ktp = j.kode
                    left join m_lokasi_nik_ind k on a.id_kota_kab_ktp = k.kode
                    left join m_lokasi_nik_ind l on a.id_kec_ktp = l.kode
                    left join m_lokasi_nik_ind m on a.id_kel_ktp = m.kode 
                    where a.id_m_user = "' . $id . '"')->getRow();
        } else if ($type_code == 'trn') {
            return $this->db->query('SELECT a.*, 
                    if(a.id_gender=1, "Laki - Laki" , "Perempuan") as nama_gender, 
                    a.namataruna as nama,
                    c.batalyon as nama_batalyon,
                    d.kompi as nama_kompi, e.peleton as nama_peleton, f.suku as nama_suku,
                    g.agama as nama_agama, h.pendidikan as nama_tkpendid, i.nama as nama_kabkota_lhr,
                    j.nama as nama_prov_ktp, k.nama as nama_kota_kab_ktp, l.nama as nama_kec_ktp, m.nama as nama_kel_ktp , n.kelompok as nama_kelompok
                    from m_user_taruna a 
                    left join m_sm_batalyon c on a.id_batalyon = c.id
                    left join m_sm_kompi d on a.id_kompi = d.id
                    left join m_sm_peleton e on a.id_peleton = e.id
                    left join m_suku f on a.id_suku = f.id
                    left join m_agama g on a.id_agama = g.id
                    left join m_tingkat_pendidikan_umum h on a.id_tkpendid = h.id
                    left join m_lokasi_lahir i on a.id_kabkota_lhr = i.kdkabkota
                    left join m_lokasi_nik_ind j on a.id_prov_ktp = j.kode
                    left join m_lokasi_nik_ind k on a.id_kota_kab_ktp = k.kode
                    left join m_lokasi_nik_ind l on a.id_kec_ktp = l.kode
                    left join m_lokasi_nik_ind m on a.id_kel_ktp = m.kode
                    left join m_kelompok n on a.id_kelompok=n.id
                    where a.id_m_user = "' . $id . '"')->getRow();
        } else {
            return null;
        }
    }
}
