<?php

namespace App\Modules\Web\Controllers;

use App\Modules\Web\Models\WebModel;
use App\Core\BaseController;

class WebPanicbutton extends BaseController
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


    // end ajax ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------


    // controller ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    public function updateproses($kdaduan, $status, $keterangan, $datausername, $texttele){

        // $idlaporan = $this->db->query("select id from t_pelaporan a where a.kd_aduan='".$kdaduan."' ")->getRow()->id;
        $cekid = $this->db->query("select a.id, b.fcm_token, a.kd_aduan from t_pelaporan a left join m_user b on a.id_user=b.id where a.id='".$kdaduan."' ")->getRow();
        $idlaporan = $cekid->id;

        $header = 'Kode aduan ' .$cekid->kd_aduan;
        

        // $id_user = $iduser;
        $id_laporan = $idlaporan;

        $insert['status'] = $status;
        $insert['id_laporan'] = $id_laporan;
        $insert['created_by'] = '1';
        $insert['created_at'] = date('Y-m-d H:i:s');
        $insert['keterangan'] = $keterangan;
        
        $builder = $this->db->table('t_proses_pelaporan');
        $execute = $builder->insert($insert);
        if($execute){
            $this->db->query("UPDATE t_pelaporan set proses='".$status."' where id='".$id_laporan."'");
            $response = [
                'success' => true,
                'status'    => 1,
                'message'   => 'Success'
                ];


            // $curl = curl_init();

            // curl_setopt_array($curl, array(
            //   CURLOPT_URL => 'http://devel.nginovasi.id/akpol-api/mobile/V1/notiftele',
            //   CURLOPT_RETURNTRANSFER => true,
            //   CURLOPT_ENCODING => '',
            //   CURLOPT_MAXREDIRS => 10,
            //   CURLOPT_TIMEOUT => 0,
            //   CURLOPT_FOLLOWLOCATION => true,
            //   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            //   CURLOPT_CUSTOMREQUEST => 'POST',
            //   CURLOPT_POSTFIELDS => array('token' => $cekid->fcm_token,'header' => $header ,'deskripsi' => $texttele),
            //   CURLOPT_HTTPHEADER => array(
            //     'Authorization: Bearer dev',
            //     'Cookie: ci_session=3j4vcvusn8gc4tl98i1lhg4dmsd3gb5v'
            //   ),
            // ));

            // $response = curl_exec($curl);

            // curl_close($curl);

            
        }else{

            $response = [
                'success' => false,
                'status' => 0,
                'message' => $this->db->error()['message']
            ];
            
        }

        echo json_encode($response);


    }
        // begin datakurikulum done

        function datapanicbutton_otorisasi()
        {
            $userid = $this->request->getPost('userid');
            $data = json_decode($this->request->getPost('param'), true);

            if ($data['status']==2) {
                $status = 'Diproses';
            } else if ($data['status']==3) {
                $status = 'Ditangani';
            } else if ($data['status']==9) {
                $status = 'Ditolak';
            }

            $this->updateproses($data['id'], $data['status'], $status, '', '');
        }

        function datapanicbutton_load()
        {
            $query = "SELECT * from (SELECT a.*, max(b.status) as status, b.keterangan from t_pelaporan a left join t_proses_pelaporan b on a.id=b.id_laporan where a.is_deleted='0' group by a.kd_aduan ) a where a.is_deleted='0' ";
            $where = ["a.kd_aduan", "a.laporan", "a.alamat", "a.keterangan"];
            $data = json_decode($this->request->getPost('param'), true);

            parent::_loadDatatable($query, $where, $data);
        }

        // function datapanicbutton_save()
        // {
        //     $userid = $this->request->getPost('userid');
        //     $data = json_decode($this->request->getPost('param'), true);
        //     parent::_insertbatch('t_bahan_ajar', $data, $userid);
        // }

        function datapanicbutton_edit()
        {
            $data = json_decode($this->request->getPost('param'), true);

            $query = "SELECT a.*, max(b.status) as status, b.keterangan from t_pelaporan a left join t_proses_pelaporan b on a.id=b.id_laporan where a.is_deleted='0' and a.id='" . $data['id'] . "'";
            parent::_edit('t_pelaporan', $data, null, $query);

        }

        // function datapanicbutton_delete()
        // {
        //     $userid = $this->request->getPost('userid');
        //     $data = json_decode($this->request->getPost('param'), true);

        //     $qr = $this->db->query("SELECT a.id_mata_pelajaran, a.id_user_pendidik  ,a.id_semester , a.id_batalyon  from t_bahan_ajar a where a.id='" . $data['id'] . "' ")->getRow();

        //     $query = "UPDATE t_bahan_ajar a set a.is_deleted='1' , last_edited_at='" . date('Y-m-d H:i:s') . "' , last_edited_by='" . $userid . "'
        //                 where a.is_deleted='0' 
        //                     and a.id_mata_pelajaran='" . $qr->id_mata_pelajaran . "'
        //                     and a.id_user_pendidik='" . $qr->id_user_pendidik . "'
        //                     and a.id_semester='" . $qr->id_semester . "'
        //                     and a.id_batalyon='" . $qr->id_batalyon . "' ";

        //     if ($this->db->query($query)) {
        //         echo json_encode(array('success' => true));
        //     } else {
        //         echo json_encode(array('success' => false, 'message' => $this->db->error()['message']));
        //     }
        // }

        // end datakurikulum done


    

    // end controller ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------





}
