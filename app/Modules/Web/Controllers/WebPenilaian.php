<?php

namespace App\Modules\Web\Controllers;

use App\Modules\Web\Models\WebModel;
use App\Core\BaseController;

class WebPenilaian extends BaseController
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

    // ajax ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------


    function jam_pertemuan_get()
    {
        $query = "select * from m_jam_pertemuan where is_weekdays = 1 and is_deleted = 0 order by jam_mulai asc";
        $result = $this->db->query($query)->getResult();

        echo json_encode($result);
    }

    // end ajax ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------


    // controller ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    function penilaiantugas_load()
    {
        $query = "SELECT a.* , b.namataruna , b.noaklong  
                from t_nilai_tugas a
                left join m_user_taruna b on a.id_user_taruna=b.id
                where a.id_jadwal_tugas='1'";
        // $where = ["b.kelompok", "b.max_kapasitas"];

        $data = $this->db->query($query)->getResult();

        echo json_encode($data);
    }

    function penilaiantugas_save()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);
        parent::_insertbatch('t_bahan_ajar_progres', $data, $userid);
    }

    function penilaiantugas_edit()
    {
        $data = json_decode($this->request->getPost('param'), true);

        $query = "select * from t_nilai_tugas where id_jadwal_tugas='1' ";

        parent::_editbatch('t_nilai_tugas', $data, null, $query);
    }

    function penilaiantugas_delete()
    {
        $userid = $this->request->getPost('userid');
        $data = json_decode($this->request->getPost('param'), true);

        $qr = $this->db->query("SELECT a.id_mata_pelajaran, a.id_user_pendidik ,a.id_tingkat ,a.id_semester , a.tahun_ajaran  from t_bahan_ajar_progres a where a.id='" . $data['id'] . "' ")->getRow();

        $query = "UPDATE t_bahan_ajar_progres a set a.is_deleted='1' , last_edited_at='" . date('Y-m-d H:i:s') . "' , last_edited_by='" . $userid . "'
                    where a.is_deleted='0' and a.id_mata_pelajaran='" . $qr->id_mata_pelajaran . "' and a.id_user_pendidik='" . $qr->id_user_pendidik . "' and a.id_tingkat='" . $qr->id_tingkat . "' and a.id_semester='" . $qr->id_semester . "' and a.tahun_ajaran='" . $qr->tahun_ajaran . "' ";

        // echo json_encode($query);
        if ($this->db->query($query)) {
            echo json_encode(array('success' => true));
        } else {
            echo json_encode(array('success' => false, 'message' => $this->db->error()['message']));
        }
    }

    // end controller ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

}
