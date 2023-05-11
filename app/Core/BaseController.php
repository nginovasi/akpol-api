<?php

namespace App\Core;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 *
 * @package CodeIgniter
 */

use CodeIgniter\Controller;
use App\Core\BaseModel;

class BaseController extends Controller
{

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = ['extension'];

    protected $session;

    private $baseModel;

    protected $db;

    // protected $adServer = "ldap://ad.nginovasi.id:389";

    // protected $adUser = "umam";

    // protected $adPass = "Semarang123!";

    // protected $adDn = "DC=nginovasi,DC=id";

    protected $adServer = "ldap://111.68.112.66:38900";

    protected $adUser = "ngi_sync@akpol.ac.id";

    protected $adPass = "NusantaraGlobalInovasi@2021";

    protected $adDn = "DC=akpol,DC=ac,DC=id";

    protected $ldap;


    /**
     * Constructor.
     */
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        //--------------------------------------------------------------------
        // Preload any models, libraries, etc, here.
        //--------------------------------------------------------------------
        // E.g.:
        // $this->session = \Config\Services::session();
        $this->baseModel = new BaseModel();
        $this->session = \Config\Services::session();
        $this->db = \Config\Database::connect();
    }

    protected function _loadSelect2($data, $query, $where)
    {
        $keyword = addslashes($data['keyword'] ?? "");
        $page = addslashes($data['page']);
        $perpage = addslashes($data['perpage']);

        $result = $this->baseModel->base_load_select2($query, $where, $keyword, $page, $perpage);

        echo json_encode(array("page" => $page, "perpage" => $perpage, "total" => count($result), "rows" => $result));
    }

    protected function _loadSelect2orderby($data, $query, $where, $orderby)
    {
        $keyword = addslashes($data['keyword'] ?? "");
        $page = addslashes($data['page']);
        $perpage = addslashes($data['perpage']);
        $orderby = addslashes($orderby);

        $result = $this->baseModel->base_load_select2orderby($query, $where, $keyword, $page, $perpage, $orderby);

        echo json_encode(array("page" => $page, "perpage" => $perpage, "total" => count($result), "rows" => $result));
    }

    protected function _loadSelect2pagging($data, $query, $where)
    {
        $keyword = addslashes($data['keyword'] ?? "");
        $page = isset($data['page']) == 0 ? 1 : addslashes($data['page']);
        $perpage = $this->db->escapeString($data['perpage']);

        $result = $this->baseModel->base_load_select2pagging($query, $where, $keyword, $page, $perpage, true);
        $result_count = $this->baseModel->base_load_select2pagging($query, $where, $keyword, $page, $perpage, false);

        echo json_encode(array("total_count" => count($result_count), "items" => $result, "incomplete_results" => true));
    }

    protected function _loadlist($data, $query)
    {

        $rs = $this->baseModel->base_load_list($query);

        if (!is_null($rs)) {
            echo json_encode(array('success' => true, 'data' => $rs));
        } else {
            echo json_encode(array('success' => false, 'message' => $this->baseModel->db->error()['message']));
        }
    }

    protected function _loadDatatable($query, $where, $data, callable $callback = NULL)
    {
        $start = $data["start"];
        $length = $data["length"];
        $search = $data["search"];;
        $key = $search["value"];

        $order = $data["order"];
        $columns = $data["columns"];
        $orderBy = [];

        foreach ($order as $o) {
            $orderColumn = $columns[$o["column"]]["data"];
            $orderDirection = $o["dir"];
            $orderBy[] = $orderColumn ." ".$orderDirection;
        }

        $result = $this->baseModel->base_load_datatable($query, $where, $key, $start, $length, implode(",", $orderBy));

        echo json_encode(array("data" => $result["data"], "recordsTotal" => $result["allData"], "recordsFiltered" => $result["filteredData"]));
    }

    protected function _loadDatatableorder($query, $where, $data, $orderby, $is_order = 'asc', callable $callback = NULL)
    {
        $start = $data["start"];
        $length = $data["length"];
        $search = $data["search"];
        $key = $search["value"];

        $order = $data["order"];
        $columns = $data["columns"];
        $orderBy = [];

        foreach ($order as $o) {
            $orderColumn = $columns[$o["column"]]["data"];
            $orderDirection = $o["dir"];
            $orderBy[] = $orderColumn ." ".$orderDirection;

            if ($orderColumn == 'id') {
                $orderColumn = $orderby;
                $orderDirection = $is_order;
                $orderBy = [ $orderColumn ." ".$orderDirection ];
                break;
            }
        }

        $result = $this->baseModel->base_load_datatable($query, $where, $key, $start, $length, implode(",", $orderBy));

        echo json_encode(array("data" => $result["data"], "recordsTotal" => $result["allData"], "recordsFiltered" => $result["filteredData"]));
    }

    protected function _otorisasi($tableName, $data, $userid, $where, $is_return= false, $field = 'verif' , callable $callback = NULL)
    {

        
        unset($data['id']);
        $data['last_edited_at'] = date('Y-m-d H:i:s');
        $data['last_edited_by'] = $userid;

        $data[$field.'_at'] = date('Y-m-d H:i:s');
        $data[$field.'_by'] = $userid;
        $data['is_'.$field] = '1';

        if ($this->baseModel->base_update($data, $tableName, $where)) {

             if ($is_return) {
                return array('success' => true, 'message' => $data);
            } else {
                echo json_encode(array('success' => true, 'message' => $data));
            }
        } else {
            if ($is_return) {
                return array('success' => false, 'message' => $this->baseModel->db->error()['message']);
            } else {
                echo json_encode(array('success' => false, 'message' => $this->baseModel->db->error()['message']));
            }

        }
    }

    protected function _insert($tableName, $data, $userid, $keys = NULL, callable $callback = NULL)
    {
        $key = $keys == NULL ? 'id' : $keys;

        if ($data[$key] == "") {
            $data['created_by'] = $userid;

            if ($this->baseModel->base_insert($data, $tableName)) {
                echo json_encode(array('success' => true, 'message' => $data));
            } else {
                echo json_encode(array('success' => false, 'message' => $this->baseModel->db->error()['message']));
            }
        } else {
            $id = $data[$key];
            $data['last_edited_at'] = date('Y-m-d H:i:s');
            $data['last_edited_by'] = $userid;
            unset($data[$key]);

            if ($this->baseModel->base_update($data, $tableName, array($key => $id))) {
                echo json_encode(array('success' => true, 'message' => $data));
            } else {
                echo json_encode(array('success' => false, 'message' => $this->baseModel->db->error()['message']));
            }
        }
    }

    protected function _insertwithid($tableName, $data, $userid, $keys = NULL, callable $callback = NULL)
    {
        $key = $keys == NULL ? 'id' : $keys;

        if ($data[$key] == "") {
            $data['created_by'] = $userid;

            if ($this->baseModel->base_insert($data, $tableName)) {
                echo json_encode(array('success' => true, 'message' => $this->baseModel->db->insertID() ));
            } else {
                echo json_encode(array('success' => false, 'message' => $this->baseModel->db->error()['message']));
            }
        } else {
            $id = $data[$key];
            $data['last_edited_at'] = date('Y-m-d H:i:s');
            $data['last_edited_by'] = $userid;
            unset($data[$key]);

            if ($this->baseModel->base_update($data, $tableName, array($key => $id))) {
                echo json_encode(array('success' => true, 'message' => $this->baseModel->db->insertID() ));
            } else {
                echo json_encode(array('success' => false, 'message' => $this->baseModel->db->error()['message']));
            }
        }
    }


    protected function _insertReturn($tableName, $data, $userid, $keys = NULL, callable $callback = NULL)
    {
        $key = $keys == NULL ? 'id' : $keys;

        if ($data[$key] == "") {
            $data['created_by'] = $userid;
            $this->db->transBegin();
            if ($this->baseModel->base_insert($data, $tableName)) {
                if ($this->db->transStatus() === false) {
                    $this->db->transRollback();
                    echo json_encode(array('success' => false, 'message' => $this->baseModel->db->error()['message']));
                } else {
                    $this->db->transCommit();
                    $id = $this->db->insertID();
                    $response = $this->baseModel->base_get($tableName, array('id' => $id))->getRow();
                    echo json_encode(array('success' => true, 'message' => $data, 'id' => $id, 'data' => $response));
                }
                // $id = $this->db->insertID();
                // $response = $this->baseModel->base_get($tableName, array('id' => $id))->getRow();
                // echo json_encode(array('success' => true, 'message' => $data, 'id' => $id, 'data' => $response));
            } else {
                echo json_encode(array('success' => false, 'message' => $this->baseModel->db->error()['message']));
            }
        } else {
            $id = $data[$key];
            $data['last_edited_at'] = date('Y-m-d H:i:s');
            $data['last_edited_by'] = $userid;
            unset($data[$key]);
            $this->db->transBegin();
            if ($this->baseModel->base_update($data, $tableName, array($key => $id))) {
                if ($this->db->transStatus() === false) {
                    $this->db->transRollback();
                    echo json_encode(array('success' => false, 'message' => $this->baseModel->db->error()['message']));
                } else {
                    $this->db->transCommit();
                    $response = $this->baseModel->base_get($tableName, array('id' => $id))->getRow();
                    echo json_encode(array('success' => true, 'message' => $data, 'data' => $response));
                }
                // $response = $this->baseModel->base_get($tableName, array('id' => $id))->getRow();
                // echo json_encode(array('success' => true, 'message' => $data, 'data' => $response));
            } else {
                echo json_encode(array('success' => false, 'message' => $this->baseModel->db->error()['message']));
            }
        }
    }

    protected function _insertbatch($tableName, $data, $userid, $keys = NULL, $is_return= false , callable $callback = NULL)
    {
        $key = $keys == NULL ? 'id' : $keys;

        $this->db->transBegin();

        $insert = array();
        $update = array();
        $delete = array();
        foreach ($data as $item) {

            if ($item['id'] == '') {
                $insertarray = $item;
                $insertarray['created_by'] = $userid;
                array_push($insert, $insertarray);
            } else {

                if (isset($item['is_deleted'])) {

                    $deletearray = $item;
                    $deletearray['last_edited_by'] = $userid;
                    $deletearray['last_edited_at'] =  date("Y-m-d H:i:s");
                    array_push($delete, $deletearray);
                } else {

                    $updatearray = $item;
                    $updatearray['last_edited_by'] = $userid;
                    $updatearray['last_edited_at'] =  date("Y-m-d H:i:s");
                    array_push($update, $updatearray);
                }
            }
        }

        if (count($insert)) {
            $this->baseModel->base_insertbatch($insert, $tableName);
        }
        if (count($update)) {
            $this->baseModel->base_updatebatch($update, $tableName, 'id');
        }
        if (count($delete)) {
            $this->baseModel->base_updatebatch($delete, $tableName, 'id');
        }

        if ($this->db->transStatus() === FALSE) {
            $this->db->transRollback();
            $this->db->transComplete();
            if ($is_return) {
                return array('success' => false, 'message' => $this->baseModel->db->error()['message']);
            } else {
                echo json_encode(array('success' => false, 'message' => $this->baseModel->db->error()['message']));
            }
        } else {
            $this->db->transCommit();
            $this->db->transComplete();
            if ($is_return) {
                return array('success' => true, 'message' => $data);
            } else {
                echo json_encode(array('success' => true, 'message' => $data));
            }
        }
    }

    protected function _edit($tableName, $data, $keys = NULL, $query = NULL)
    {
        $key = $keys == NULL ? 'id' : $keys;
        $rs = $query == NULL ? $this->baseModel->base_get($tableName, [$key => $data[$key]])->getRow() : $this->baseModel->db->query($query)->getRow();

        if (!is_null($rs)) {
            echo json_encode(array('success' => true, 'data' => $rs));
        } else {
            echo json_encode(array('success' => false, 'message' => $this->baseModel->db->error()['message']));
        }
    }

    protected function _editbatch($tableName, $data, $keys = NULL, $query = NULL)
    {
        $key = $keys == NULL ? 'id' : $keys;
        $rs = $query == NULL ? $this->baseModel->base_get($tableName, [$key => $data[$key]])->getRow() : $this->baseModel->db->query($query)->getRow();

        $rslist = $this->baseModel->base_load_list($query);

        // die;

        if (!is_null($rs)) {
            if (!is_null($rslist)) {
                echo json_encode(array('success' => true, 'data' => $rs, 'list' => $rslist));
            } else {
                echo json_encode(array('success' => false, 'message' => 'Taruna Tidak Ditemukan'));
            }
            // echo json_encode(array('success' => true, 'data' => $rs ));
        } else {
            echo json_encode(array('success' => false, 'message' => $this->baseModel->db->error()['message']));
        }
    }

    protected function _delete($tableName, $data, $userid, $keys = NULL)
    {
        $key = $keys == NULL ? 'id' : $keys;
        if ($this->baseModel->base_delete($tableName, [$key => $data[$key]], $userid)) {
            echo json_encode(array('success' => true));
        } else {
            echo json_encode(array('success' => false, 'message' => $this->baseModel->db->error()['message']));
        }
    }

    protected function _ldapBind(callable $callback = NULL)
    {
        putenv('LDAPTLS_REQCERT=never');

        $this->ldap = ldap_connect($this->adServer);

        ldap_set_option($this->ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($this->ldap, LDAP_OPT_REFERRALS, 0);

        $bind = @ldap_bind($this->ldap, $this->adUser, $this->adPass);

        if($bind){
            if($callback != NULL) { $callback(array('success' => true)); } else { $callback(array('success' => false, 'message' => 'Callback is needed')); }
        }else{
            $callback(array('success' => false, 'message' => ldap_error($this->ldap)));
        }

        @ldap_close($this->ldap);
    }

    protected function _ldapLogin($username, $password, $callback)
    {
        $this->_ldapBind(function($result) use ($username, $password, $callback) {
            if($result['success']){
                $query = @ldap_search($this->ldap, $this->adDn, "(&(|(cn=$username)(mail=$username)(samaccountname=$username)(userprincipalname=".$username."@akpol.ac.id))(userPassword=$password))") or die(json_encode(['success' => false, 'message' => "Error in search query: " . ldap_error($this->ldap)]));
                // $query = @ldap_search($this->ldap, $this->adDn, "(objectclass=user)") or die(json_encode(['success' => false, 'message' => "Error in search query: " . ldap_error($this->ldap)]));

                $result = ldap_get_entries($this->ldap, $query);
                $data = $this->utf8ize($this->cleanUpEntry($result));

                if(count($data) > 0){
                    $keys = array_keys($data);
                    $currentKey;

                    foreach ($keys as $key) {
                        // if(strpos($key, $username) !== false){
                        //     $currentKey = $key;
                        //     break;
                        // }

                        $currentKey = $key;
                        break;
                    }

                    $callback(['success' => true, 'data' => $data[$currentKey], 'dn' => $currentKey]);
                }else{
                    $callback(['success' => false, 'message' => "Username & Password Salah"]);
                }
            }else{
                $callback($result);
            }
        });
    }

    protected function _ldapAdd($data, $callback)
    {
        $this->_ldapBind(function($result) use ($data, $callback) {
            if($result['success']){
                $username = $data['username'];
                $password = $data['password'];
                $name = $data['name'];
                $email = $data['email'];
                $group = "admin";
                $tahun = $data['tahun'] == "" ? "" : "OU=".$data['tahun'].",";
                $arrName = explode(" ", $name);

                switch (strtolower($data['type_code'] ?? "")) {
                    case 'trn':
                        $group = "taruna";
                        break;
                    case 'gdk':
                        $group = "pendidik";
                        break;
                    default:
                        $group = "admin";
                        break;
                }

                $sn = $tahun."OU=".$group.",".$this->adDn;
                $dn_user="CN=".$username.",".$sn;

                $ldaprecord['mail'] = $email;
                $ldaprecord['userPassword'] = base64_encode($password);

                $query = @ldap_search($this->ldap, $this->adDn, "(|(userprincipalname=".$username."@akpol.ac.id)(userprincipalname=".$username."@ad.nginovasi.id))") or die(json_encode(['success' => false, 'message' => "Error in search query: " . ldap_error($this->ldap)]));
                // $query = @ldap_search($this->ldap, $this->adDn, "(objectclass=user)") or die(json_encode(['success' => false, 'message' => "Error in search query: " . ldap_error($this->ldap)]));

                $results = ldap_get_entries($this->ldap, $query);
                $data = $this->utf8ize($this->cleanUpEntry($results));

                if(count($data) > 0){
                    $keys = array_keys($data);
                    $dn_user;

                    foreach ($keys as $key) {
                        $dn_user = $key;
                    }
                    
                    $modify = @ldap_modify($this->ldap, $dn_user, $ldaprecord);

                    if($modify) {
                        $callback(array("success" => true));
                    } else {
                        $callback(["success" => false, "message" => ldap_error($this->ldap), "event" => "on modify", "dn_user" => $dn_user]);
                    }
                }else{
                    $usernameArr = explode("@", $username);

                    $ldaprecord['cn'] = $username;
                    $ldaprecord['givenName'] = chop($name, end($arrName)) == "" ? end($arrName) : chop($name, end($arrName));  
                    $ldaprecord['sn'] = end($arrName);
                    $ldaprecord['sAMAccountName'] = substr($username, 0, 20);
                    $ldaprecord['UserPrincipalName'] = @$usernameArr[0]."@akpol.ac.id";
                    $ldaprecord['UserAccountControl'] = "544";
                    $ldaprecord['objectclass'][0] = 'top';
                    $ldaprecord['objectclass'][1] = 'person';
                    $ldaprecord['objectclass'][2] = 'organizationalPerson';
                    $ldaprecord['objectclass'][3] = 'user';
                    $ldaprecord['displayName'] = $name;
                    $ldaprecord['name'] = $name;
                    // $ldaprecord['userPassword'] = base64_encode($password);

                    $add = @ldap_add($this->ldap, $dn_user, $ldaprecord);

                    if($add) {
                        $callback(array("success" => true));
                    } else {
                        $callback(array("success" => false, "message" => ldap_error($this->ldap), "event" => "on add user", "dn_user" => $dn_user));
                    }
                }
            }else{
                echo json_encode($result);
            }
        });
    }

    protected function _ldapAddOu($data, $callback)
    {
        $this->_ldapBind(function($result) use ($data, $callback) {
            if($result['success']){
                $tahun = $data['tahun_masuk'];

                $dn = "OU=".$tahun.",OU=taruna,".$this->adDn;
                $query = @ldap_search($this->ldap, $this->adDn, "(distinguishedname=$dn)") or die(json_encode(['success' => false, 'message' => "Error in search query: " . ldap_error($this->ldap)]));

                $results = ldap_get_entries($this->ldap, $query);
                $data = $this->utf8ize($this->cleanUpEntry($results));

                if(count($data) > 0){
                    $callback(array("success" => true));
                }else{
                    $ldaprecord['ou'] = $tahun;
                    $ldaprecord['name'] = $tahun;
                    $ldaprecord['objectclass'][0] = 'top';
                    $ldaprecord['objectclass'][1] = 'organizationalUnit';

                    $add = @ldap_add($this->ldap, $dn, $ldaprecord);

                    if($add) {
                        $callback(array("success" => true));
                    } else {
                        $callback(array("success" => false, "message" => ldap_error($this->ldap), "event" => "on add ou"));
                    }
                }
            }else{
                echo json_encode($result);
            }
        });

    }

    protected function _ldapModify($data, $record, $callback)
    { 
        $this->_ldapBind(function($result) use ($data, $record, $callback) {
            if($result['success']){
                $username = $data['username'];
                $password = $data['old_password'];
                $encodePwd = base64_encode($password);

                $query = @ldap_search($this->ldap, $this->adDn, "(&(cn=$username)(userPassword=$encodePwd))") or die(json_encode(['success' => false, 'message' => "Error in search query: " . ldap_error($this->ldap)]));
                $result = ldap_get_entries($this->ldap, $query);
                $data = $this->utf8ize($this->cleanUpEntry($result));

                if(count($data) > 0){
                    $keys = array_keys($data);
                    $dn_user;

                    foreach ($keys as $key) {
                        if(strpos($key, $username) !== false){
                            $dn_user = $key;
                            break;
                        }
                    }

                    $modify = @ldap_modify($this->ldap, $dn_user, $record);

                    if($modify) {
                        $callback(array("success" => true));
                    } else {
                        $callback(["success" => false, "error" => ldap_error($this->ldap), "event" => "on modify"]);
                    }
                }else{
                    $callback(['success' => false, 'message' => "Tidak Dapat Memperoleh Kredensial"]);
                }
            }else{
                echo json_encode($result);
            }
        });
    }

    protected function cleanUpEntry($entry)
    {
        $retEntry = array();
        for ($i = 0; $i < $entry['count']; $i++) {
            if (is_array($entry[$i])) {
                $subtree = $entry[$i];
                //This condition should be superfluous so just take the recursive call
                //adapted to your situation in order to increase perf.
                if (!empty($subtree['dn']) && !isset($retEntry[$subtree['dn']])) {
                    $retEntry[$subtree['dn']] = $this->cleanUpEntry($subtree);
                } else {
                    $retEntry[] = $this->cleanUpEntry($subtree);
                }
            } else {
                $attribute = $entry[$i];
                if ($entry[$attribute]['count'] == 1) {
                    $retEntry[$attribute] = $entry[$attribute][0];
                } else {
                    for ($j = 0; $j < $entry[$attribute]['count']; $j++) {
                        $retEntry[$attribute][] = mb_convert_encoding($entry[$attribute][$j], 'UTF-8', 'UTF-8');
                    }
                }
            }
        }

        return $retEntry;
    }

    protected function utf8ize($mixed)
    {
        if (is_array($mixed)) {
            foreach ($mixed as $key => $value) {
                $mixed[$key] = $this->utf8ize($value);
            }
        } elseif (is_string($mixed)) {
            return mb_convert_encoding($mixed, "UTF-8", "UTF-8");
        }

        return $mixed;
    }
}
