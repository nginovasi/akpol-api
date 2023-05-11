<?php namespace App\Modules\Mobile\Models;

use App\Core\BaseModel;

class MobileModel extends BaseModel
{

	function __construct() {
		parent::__construct();
	}

    //model IOT
    public function getTaruna($noak){
        return $this->base_get('m_user_taruna', ['is_verif' => 1, 'is_deleted' => 0, 'noaklong' => $noak])->getRow();
    }
    
    //model API Mobile

    public function getSemester(){
        return $this->db->query('SELECT * FROM m_semester'); 
    }

    public function getBatalyonID($id){
        return $this->db->query('SELECT * FROM m_sm_batalyon where id="'.$id.'" '); 
    }

    public function getProvinsi(){
        return $this->db->query("SELECT * from (select lpad(kode,2,0) as kodeprov,nama from m_lokasi_nik_ind) a
                                    group by a.kodeprov");
    }

    public function getKota($idprov){
        return $this->db->query("SELECT * from (SELECT lpad(kode,5,0) as kdkabkota,nama from m_lokasi_nik_ind ) a 
                                    where a.kdkabkota like '%".$idprov."."."%'
                                    group by a.kdkabkota");
    }
    public function getKec($idkota){
        return $this->db->query("SELECT * from (SELECT lpad(kode,8,0) as kdkec,nama from m_lokasi_nik_ind ) a 
                                    where a.kdkec like '%".$idkota."."."%'
                                    group by a.kdkec");
    }
    public function getKel($idkec){
        return $this->db->query("SELECT * from (SELECT lpad(kode,13,0) as kdkec,nama from m_lokasi_nik_ind ) a 
                                    where a.kdkec like '%".$idkec."."."%'
                                    group by a.kdkec");
    }
}