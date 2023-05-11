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

    public function getOTP()
    {
        $kode = 'OTP';
        $otp = $this->db->query("SELECT nilai FROM m_config where kode='".$kode."'")->getRow();

        $is_otp = $otp->nilai;
        
        return $is_otp;
    }

    public function authUserPassword()
    {
        $username = $_POST['username'];
        $password = base64_encode($_POST['password']);
        $tipe = $_POST['tipe'];
        $that = $this;

        parent::_ldapLogin($username, $password, function($result) use ($tipe, $username, $that){
            if($result['success']){
                $isotp = $that->getOTP();
        
                $query = $that->db->query("SELECT a.*,b.name as type_name from m_user a 
                                          LEFT JOIN m_user_type b on a.type_code=b.code
                                          where a.username='".$username."'");
                $resData = $query->getRow();

                if($resData != NULL){           
                    $tipeuser = $resData->type_code;
                    $tipename = $resData->type_name;
                    $id_user = $resData->id;
                    $is_active = $resData->is_deleted;
                    if($is_active == 0){

                        // if(strpos($result['dn'], $tipe) !== false){
                            if($tipeuser == 'gdk'){
                                $userData = $that->db->query("SELECT a.id as id_user,a.email,b.nik,b.namagadik,b.`photopath`,b.`nrp`,b.pangkat,b.jab,b.telp,b.`is_gadik`,b.`is_instruktur`,b.`is_internal`,b.`is_pengasuh`,b.`folder_materi` from m_user a
                                            LEFT JOIN m_user_pendidik b on a.id=b.`id_m_user`
                                            where a.id='".$id_user."'");
                                
                                $cekData = $userData->getFieldCount();
                
                                if($cekData > 0){
                                    $response = [
                                                'status' => 1,
                                                'message' => 'Success',
                                                'data' => $userData->getRow(),
                                                'type_code' => $tipeuser,
                                                'type_name' => $tipename,
                                                'is_otp' => $isotp

                                                ];

                                    echo json_encode($response);
                                }else{
                                    $response = [
                                                'status' => 0,
                                                'message' => 'Data Detail User Tidak Ditemukan'
                                                ];

                                    echo json_encode($response);
                                }
                            }else{
                                $userData = $that->db->query("SELECT a.id as id_user,b.namataruna,a.email,b.telp,b.nik,b.noakshort,b.noaklong,b.photopath,c.jabatan,g.tingkatan,d.`batalyon`,e.`kompi`,f.`peleton`,b.folder_materi from m_user a
                                            LEFT JOIN m_user_taruna b on a.id=b.id_m_user
                                            LEFT JOIN m_tingkatan_detail c on b.id_tingkat=c.id
                                            LEFT JOIN m_tingkatan g on c.id_tingkatan=g.id
                                            LEFT JOIN m_sm_batalyon d on b.id_batalyon=d.id
                                            LEFT JOIN m_sm_kompi e on b.id_kompi=e.id
                                            LEFT JOIN m_sm_peleton f on b.id_peleton=f.id
                                            where a.id='".$id_user."'");
                                
                                $cekData = $userData->getFieldCount();
                
                                if($cekData > 0){
                                    $response = [
                                                'status' => 1,
                                                'message' => 'Success',
                                                'data' => $userData->getRow(),
                                                'type_code' => $tipeuser,
                                                'type_name' => $tipename,
                                                'is_otp' => $isotp
                                                ];

                                    echo json_encode($response);
                                }else{
                                    $response = [
                                                'status' => 0,
                                                'message' => 'Data Detail User Tidak Ditemukan'
                                                ];

                                    echo json_encode($response);
                                }
                            }
                        // }else{
                        //     $response = [
                        //     'status' => 0,
                        //     'message' => 'User Tidak Ditemukan'
                        //     ];

                        // echo json_encode($response);
                        // }
                    }else{
                        $response = [
                            'status' => 0,
                            'message' => 'User Tidak Aktif'
                            ];

                        echo json_encode($response);
                    }
                }else{
                    $response = [
                                'status' => 0,
                                'message' => 'User Tidak Terdaftar'
                                ];

                    echo json_encode($response);
                }
            }else{
                $response = [
                        'status' => 0,
                        'message' => $result["message"]
                ];

                echo json_encode($response);
            }
        });
    }

    public function changepass(){
        $data = json_decode($this->request->getPost('param'), true);
        $edited["userPassword"] = base64_encode($data["new_password"]);

        parent::_ldapModify($data, $edited, function($result){
            $result["message"] = !$result["success"] ? "Password Lama Salah" : "";

            echo json_encode($result);
        });
    }
}