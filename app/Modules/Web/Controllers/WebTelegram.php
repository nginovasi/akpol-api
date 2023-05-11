<?php

namespace App\Modules\Web\Controllers;

use App\Modules\Web\Models\WebModel;
use App\Core\BaseController;

class WebTelegram extends BaseController
{
    private $webModel;

    // var $tokenbottele = "5565931042:AAEIaq1eWs9_JbLKBi10d9iG87IuplFWxuo"; // tesnet
    var $tokenbottele = "5335283998:AAHEyZmjB__VQNZw767DxQUXDxlG2Hdsw3c"; // bot akpol

    // var $eduakpol = '-680205430';
    var $eduakpol = '-1001182539588';
    

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
        // echo "WebTelegram";
    }

    // controller ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    public function BotTelegram(){

        $update = json_decode(file_get_contents('php://input'), true);

        $buttonlaporan = json_encode([
                                    "inline_keyboard" => [
                                        [
                                            [
                                                "text" => "Abaikan",
                                                "callback_data" => "abaikanlaporan"
                                            ],
                                            [
                                                "text" => "Tindak",
                                                "callback_data" => "tindaklaporan"
                                            ]
                                        ]
                                    ]
                                ]);

        $buttonselesai = json_encode([
                        "inline_keyboard" => [
                            [
                                [
                                    "text" => "Selesai ",
                                    "callback_data" => "selesai"
                                ]
                            ]
                        ]
                    ]);

        $konfirmasiabaikan = json_encode([
                        "inline_keyboard" => [
                            [
                                [
                                    "text" => "Tolak",
                                    "callback_data" => "konfirmasiabaikantolak"
                                ],
                                [
                                    "text" => "Kembali",
                                    "callback_data" => "konfirmasiabaikantidakjadi"
                                ]
                            ]
                        ]
                    ]);

        $konfirmasitindaklaporan = json_encode([
                        "inline_keyboard" => [
                            [
                                [
                                    "text" => "Ya",
                                    "callback_data" => "konfirmasitindaklaporanya"
                                ],
                                [
                                    "text" => "Tidak",
                                    "callback_data" => "konfirmasitindaklaporantidakjadi"
                                ]
                            ]
                        ]
                    ]);
        $buttonbidang = json_encode([
                        "inline_keyboard" => [
                            [
                                [
                                    "text" => "Sudah",
                                    "callback_data" => "konfirmasibidangsudah"
                                ],
                                [
                                    "text" => "Bidang menolak",
                                    "callback_data" => "konfirmasibidangtolak"
                                ]
                            ]
                        ]
                    ]);

    $botAPI = "https://api.telegram.org/bot" . $this->tokenbottele;

    if (isset($update['callback_query'])) {

        $datacallbackmessage = $update['callback_query']['message'];
        $datacallback = $update['callback_query']['message'];

        $datausername = $update['callback_query']['from']['username'];
        $datafirst_name = $update['callback_query']['from']['first_name'];
        $datalast_name = $update['callback_query']['from']['last_name'];


            if ($update['callback_query']['data'] === 'tindaklaporan') {

                $getidtrx = $this->get_string_between($datacallbackmessage['text'], ': *', '*');
                $isipesan = urlencode($datacallbackmessage['text']).'%0D%0A%0D%0AAnda ingin menerima laporan dengan trx '.$getidtrx.' ini ?'; 

                file_get_contents($botAPI . "/editMessageText?chat_id=".$update["callback_query"]["message"]["chat"]['id']."&message_id=".$update['callback_query']['message']['message_id']."&text=$isipesan&parse_mode=HTML&reply_markup={$konfirmasitindaklaporan}");

            } else if ($update['callback_query']['data'] === 'abaikanlaporan') {


                $getidtrx = $this->get_string_between($datacallbackmessage['text'], ': *', '*');
                $isipesan = urlencode($datacallbackmessage['text']).'%0D%0A%0D%0AAnda ingin menolak laporan dengan trx '.$getidtrx.' ini ?'; 

                // $this->updateproses($getidtrx, '3', 'Ditangani', $datausername);
                // $this->updateproses($getidtrx, '9', 'Ditolak', $datausername);

                file_get_contents($botAPI . "/editMessageText?chat_id=".$update["callback_query"]["message"]["chat"]['id']."&message_id=".$update['callback_query']['message']['message_id']."&text=$isipesan&parse_mode=HTML&reply_markup={$konfirmasiabaikan}");


            } else if ($update['callback_query']['data']==='konfirmasiabaikantolak') {
                // jadi di abaikan

                $getidtrx = $this->get_string_between($datacallbackmessage['text'], ': *', '*');
                $isipesan = str_replace( '

Anda ingin menolak laporan dengan trx '.$getidtrx.' ini ?' , '

Ditolak di SIAK oleh nama '.$datafirst_name.' '.$datalast_name.', Username @'.$datausername .' Jam '. date("d-m-Y H:i:s") ,$datacallbackmessage['text']);


                file_get_contents($botAPI . "/editMessageText?chat_id=".$update["callback_query"]["message"]["chat"]['id']."&message_id=".$update['callback_query']['message']['message_id']."&text=".urlencode($isipesan)."&parse_mode=HTML");

                $texttele = 'Ditolak di SIAK oleh nama '.$datafirst_name.' '.$datalast_name.', Username @'.$datausername .' Jam '. date("d-m-Y H:i:s");

                $query_log = $this->db->query("INSERT INTO `log_telegram` (`text`, `chat_id`, `message_id`, `pesan`) VALUES ('".json_encode($update)."', '".$update["callback_query"]["message"]["chat"]['id']."', '".$update['callback_query']['message']['message_id']."', '".urldecode($isipesan)."')");

                $this->updateproses($getidtrx, '9', 'Ditolak', $datausername, $texttele, $update['callback_query']['message']['message_id']);

            } else if ($update['callback_query']['data']==='konfirmasiabaikantidakjadi') {
                // tidak jadi di abaikan
                $getidtrx = $this->get_string_between($datacallbackmessage['text'], ': *', '*');
                $isipesan = str_replace( '

Anda ingin menolak laporan dengan trx '.$getidtrx.' ini ?' , ' ' ,$datacallbackmessage['text']);

                $query_log = $this->db->query("INSERT INTO `log_telegram` (`text`, `chat_id`, `message_id`, `pesan`) VALUES ('".json_encode($update)."', '".$update["callback_query"]["message"]["chat"]['id']."', '".$update['callback_query']['message']['message_id']."', '".urldecode($isipesan)."')");

                file_get_contents($botAPI . "/editMessageText?chat_id=".$update["callback_query"]["message"]["chat"]['id']."&message_id=".$update['callback_query']['message']['message_id']."&text=".urlencode($isipesan)."&parse_mode=HTML&reply_markup={$buttonlaporan}");

            } else if ($update['callback_query']['data']==='konfirmasitindaklaporanya') {
                // jadi di abaikan

                $getidtrx = $this->get_string_between($datacallbackmessage['text'], ': *', '*');
                $isipesan = str_replace( '

Anda ingin menerima laporan dengan trx '.$getidtrx.' ini ?' , '

Diterima di SIAK oleh nama '.$datafirst_name.' '.$datalast_name.', Username @'.$datausername .'. Jam '. date("d-m-Y H:i:s").'.

Apakah anda sudah menghubungi bidang ?' ,$datacallbackmessage['text']);

                file_get_contents($botAPI . "/editMessageText?chat_id=".$update["callback_query"]["message"]["chat"]['id']."&message_id=".$update['callback_query']['message']['message_id']."&text=".urlencode($isipesan)."&parse_mode=HTML&reply_markup={$buttonbidang}");

                $texttele = 'Diterima di SIAK oleh nama '.$datafirst_name.' '.$datalast_name.', Username @'.$datausername .'. Jam '. date("d-m-Y H:i:s");

                $query_log = $this->db->query("INSERT INTO `log_telegram` (`text`, `chat_id`, `message_id`, `pesan`) VALUES ('".json_encode($update)."', '".$update["callback_query"]["message"]["chat"]['id']."', '".$update['callback_query']['message']['message_id']."', '".urldecode($isipesan)."')");

                $this->updateproses($getidtrx, '2', 'Diproses', $datausername, $texttele, $update['callback_query']['message']['message_id']);


            } else if ($update['callback_query']['data']==='konfirmasitindaklaporantidakjadi') {
                // tidak jadi di abaikan
                $getidtrx = $this->get_string_between($datacallbackmessage['text'], ': *', '*');
                $isipesan = str_replace( '

Anda ingin menerima laporan dengan trx '.$getidtrx.' ini ?' , ' ' ,$datacallbackmessage['text']);

                $query_log = $this->db->query("INSERT INTO `log_telegram` (`text`, `chat_id`, `message_id`, `pesan`) VALUES ('".json_encode($update)."', '".$update["callback_query"]["message"]["chat"]['id']."', '".$update['callback_query']['message']['message_id']."', '".urldecode($isipesan)."')");

                file_get_contents($botAPI . "/editMessageText?chat_id=".$update["callback_query"]["message"]["chat"]['id']."&message_id=".$update['callback_query']['message']['message_id']."&text=".urlencode($isipesan)."&parse_mode=HTML&reply_markup={$buttonlaporan}");

            } else if ($update['callback_query']['data']==='konfirmasibidangsudah') {
                
                $getidtrx = $this->get_string_between($datacallbackmessage['text'], ': *', '*');
                $isipesan = str_replace( '

Apakah anda sudah menghubungi bidang ?' , '

Diteruskan di Bidang oleh nama '.$datafirst_name.' '.$datalast_name.', Username @'.$datausername .'. Jam '. date("d-m-Y H:i:s").'.' ,$datacallbackmessage['text']);
                file_get_contents($botAPI . "/editMessageText?chat_id=".$update["callback_query"]["message"]["chat"]['id']."&message_id=".$update['callback_query']['message']['message_id']."&text=".urlencode($isipesan)."&parse_mode=HTML&reply_markup={$buttonselesai}");
                $texttele = 'Diteruskan di Bidang oleh nama '.$datafirst_name.' '.$datalast_name.', Username @'.$datausername .'. Jam '. date("d-m-Y H:i:s");

                $query_log = $this->db->query("INSERT INTO `log_telegram` (`text`, `chat_id`, `message_id`, `pesan`) VALUES ('".json_encode($update)."', '".$update["callback_query"]["message"]["chat"]['id']."', '".$update['callback_query']['message']['message_id']."', '".urldecode($isipesan)."')");

                $this->updateproses($getidtrx, '3', 'Ditangani', $datausername, $texttele, $update['callback_query']['message']['message_id']);

            } else if ($update['callback_query']['data']==='konfirmasibidangtolak') {
                
                $getidtrx = $this->get_string_between($datacallbackmessage['text'], ': *', '*');
                $isipesan = str_replace( '

Apakah anda sudah menghubungi bidang ?' , '

Ditolak di Bidang oleh nama '.$datafirst_name.' '.$datalast_name.', Username @'.$datausername .' Jam '. date("d-m-Y H:i:s") ,$datacallbackmessage['text']);


                file_get_contents($botAPI . "/editMessageText?chat_id=".$update["callback_query"]["message"]["chat"]['id']."&message_id=".$update['callback_query']['message']['message_id']."&text=".urlencode($isipesan)."&parse_mode=HTML");
                $texttele = 'Ditolak di Bidang oleh nama '.$datafirst_name.' '.$datalast_name.', Username @'.$datausername .' Jam '. date("d-m-Y H:i:s");

                $query_log = $this->db->query("INSERT INTO `log_telegram` (`text`, `chat_id`, `message_id`, `pesan`) VALUES ('".json_encode($update)."', '".$update["callback_query"]["message"]["chat"]['id']."', '".$update['callback_query']['message']['message_id']."', '".urldecode($isipesan)."')");


                $this->updateproses($getidtrx, '9', 'Ditolak', $datausername, $texttele, $update['callback_query']['message']['message_id']);

            } else if ($update['callback_query']['data']==='selesai') {
                
                $getidtrx = $this->get_string_between($datacallbackmessage['text'], ': *', '*');
                $isipesan = urlencode($datacallbackmessage['text']).'%0D%0A%0D%0ALaporan Selesai pada '. date("d-m-Y H:i:s"); 

                file_get_contents($botAPI . "/editMessageText?chat_id=".$update["callback_query"]["message"]["chat"]['id']."&message_id=".$update['callback_query']['message']['message_id']."&text=$isipesan&parse_mode=HTML");
                $texttele = 'Selesai';

                $query_log = $this->db->query("INSERT INTO `log_telegram` (`text`, `chat_id`, `message_id`, `pesan`) VALUES ('".json_encode($update)."', '".$update["callback_query"]["message"]["chat"]['id']."', '".$update['callback_query']['message']['message_id']."', '".urldecode($isipesan)."')");

                $this->updateproses($getidtrx, '4', 'Selesai', $datausername, $texttele, $update['callback_query']['message']['message_id']);

            }



    }

        $msg = $update['message']['text'];
        if ($msg === "/start") {

            $data = http_build_query([
                'text' => json_encode($update["message"]). '%0D%0A' .$update["message"]["chat"]["id"],
                'chat_id' => $update["message"]["chat"]["id"]
            ]);
            $keyboard = json_encode([
                "inline_keyboard" => [
                    [
                        [
                            "text" => "Ya",
                            "callback_data" => "Ya"
                        ],
                        [
                            "text" => "Tidak",
                            "callback_data" => "No"
                        ]
                    ]
                ]
            ]);

            file_get_contents($botAPI . "/sendMessage?{$data}&reply_markup={$keyboard}");
        } else if ($msg === "/test") {
            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => 'http://devel.nginovasi.id/akpol-api/web/telegram/addPanicButton',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS =>'{
                "text" : "KD aduan : *<b>P32202206151290001</b>*\\nSaya butuh ambulan secepatnya.",
                "location" : {
                    "latitude" : -7.002951,
                    "longitude" : 110.256549
                },
                "photo" : "http://siap.akpol.ac.id/assets/img/AKPOL%20Logo-min.png"
            }',
              CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer dev',
                'Content-Type: application/json',
                'Cookie: ci_session=rgcfusdhia8uc530pb1os0c17lt8cmnr'
              ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            echo $response;
        }

    }

    public function addPanicButton(){
        $json = json_decode(file_get_contents('php://input'), true);

        $keyboard = json_encode([
                                    "inline_keyboard" => [
                                        [
                                            [
                                                "text" => "Abaikan",
                                                "callback_data" => "abaikanlaporan"
                                            ],
                                            [
                                                "text" => "Tindak",
                                                "callback_data" => "tindaklaporan"
                                            ]
                                        ]
                                    ]
                                ]);
        if ($json['location']!=='') {
            // $lok = $json['location']['latitude'];
            // echo json_encode($lok);


            $link = "https://api.telegram.org/bot".$this->tokenbottele."/sendlocation?chat_id=".$this->eduakpol."&latitude=".$json['location']['latitude']."&longitude=".$json['location']['longitude']."";

            $rstele =  json_decode($this->chat_tele($link), true);

            // echo json_encode($rstele);
            if ($rstele['ok']) {

                
                if ($json['photo']!=='') {

                    $link = "https://api.telegram.org/bot".$this->tokenbottele."/sendPhoto?chat_id=".$this->eduakpol."&photo=".$json['photo']."&parse_mode=HTML&reply_to_message_id=".$rstele['result']['message_id'];

                    $rstelepoto =  json_decode($this->chat_tele($link), true);

                    if ($rstelepoto['ok']) {

                        $link = "https://api.telegram.org/bot".$this->tokenbottele."/sendMessage?chat_id=".$this->eduakpol."&text=".urlencode($json['text'])."&parse_mode=HTML&reply_markup={$keyboard}&reply_to_message_id=".$rstelepoto['result']['message_id'];

                        echo json_encode($link);
                        $rstele =  json_decode($this->chat_tele($link), true);
                    
                    }

                } else {

                        $link = "https://api.telegram.org/bot".$this->tokenbottele."/sendMessage?chat_id=".$this->eduakpol."&text=".urlencode($json['text'])."&parse_mode=HTML&reply_markup={$keyboard}&reply_to_message_id=".$rstele['result']['message_id'];
                    $rstele =  json_decode($this->chat_tele($link), true);
               
                }


            } else {

            }

        } else {

            if ($json['photo']!=='') {
                    // $poto = $json['photo'];

                    // echo $poto;
                    $link = "https://api.telegram.org/bot".$this->tokenbottele."/sendPhoto?chat_id=".$this->eduakpol."&photo=".$json['photo']."&caption=".urlencode($json['text'])."&parse_mode=HTML&reply_markup={$keyboard}";
                    $rstele =  json_decode($this->chat_tele($link), true);

                } else {

                    $link = "https://api.telegram.org/bot".$this->tokenbottele."/sendMessage?chat_id=".$this->eduakpol."&text=".urlencode($json['text'])."&parse_mode=HTML&reply_markup={$keyboard}";
                    $rstele =  json_decode($this->chat_tele($link), true);
               
                }

        }

    }


    function chat_tele($link) {

        

        $headerstele = array();
        $headerstele[] = 'Content-Type: application/json';


        $chtele = curl_init();
        curl_setopt($chtele, CURLOPT_URL, $link);
        curl_setopt($chtele, CURLOPT_CUSTOMREQUEST,"POST");
        curl_setopt($chtele, CURLOPT_HTTPHEADER,$headerstele);
        curl_setopt($chtele, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($chtele, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($chtele, CURLOPT_RETURNTRANSFER, true);
        //Send the request
        $responsetele = curl_exec($chtele);
        //Close request
        if ($responsetele === FALSE) {
          die('FCM Send Error: ' . curl_error($chtele));
        }


        return $responsetele;

        curl_close($chtele);
    }


    function get_string_between($string, $start, $end){
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    function tindaklaporan(){
        $query_log = $this->db->query("SELECT a.* from log_telegram a where text like '%220622017%' order by a.id DESC limit 1")->getRow();

        $isipesan = urlencode($query_log->pesan.'

Laporan Selesai pada '. date("d-m-Y H:i:s")); 

        $botAPI = "https://api.telegram.org/bot" . $this->tokenbottele;

        file_get_contents($botAPI . "/editMessageText?chat_id=".$query_log->chat_id."&message_id=".$query_log->message_id."&text=$isipesan&parse_mode=HTML");
    }

    public function updateproses($kdaduan, $status, $keterangan, $datausername, $texttele, $message_id ){

        $created_by = $this->db->query("select id from m_pic_pelaporan a where a.username_tele='".$datausername."' ")->getRow()->id;
        $cekid = $this->db->query("select a.id, b.fcm_token from t_pelaporan a left join m_user b on a.id_user=b.id where a.kd_aduan='".$kdaduan."' ")->getRow();
        $idlaporan = $cekid->id;

        $header = 'Kode aduan ' .$kdaduan;
        

        // $id_user = $iduser;
        $id_laporan = $idlaporan;

        $insert['status'] = $status;
        $insert['id_laporan'] = $id_laporan;
        $insert['created_by'] = $created_by;
        $insert['created_at'] = date('Y-m-d H:i:s');
        $insert['keterangan'] = $keterangan;
        
        $builder = $this->db->table('t_proses_pelaporan');
        $execute = $builder->insert($insert);
        if($execute){
            $this->db->query("UPDATE t_pelaporan set proses='".$status."', message_id_telegram='".$message_id."' where id='".$id_laporan."'");
            $response = [
                'status'    => 1,
                'message'   => 'Success'
                ];


            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => 'http://devel.nginovasi.id/akpol-api/mobile/V1/notiftele',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS => array('token' => $cekid->fcm_token,'header' => $header ,'deskripsi' => $texttele),
              CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer dev',
                'Cookie: ci_session=3j4vcvusn8gc4tl98i1lhg4dmsd3gb5v'
              ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);

        }else{
            $response = [
                'status' => 0,
                'message' => 'Failed Insert'
            ];
        }


    }



    // ajax ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

}
