<?php

namespace App\Modules\Web\Controllers;

use App\Modules\Web\Models\WebModel;
use App\Core\BaseController;

class WebLdap extends BaseController
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

    function login(){
        $username = $this->request->getPost('username');
        $password = base64_encode($this->request->getPost('password'));

        $this->_ldapBind(function($result) use ($username, $password) {
            if($result['success']){
                $query = @ldap_search($this->ldap, $this->adDn, "(&(cn=$username)(userPassword=$password))") or die(json_encode(['success' => false, 'message' => "Error in search query: " . ldap_error($this->ldap)]));
                // $query = @ldap_search($this->ldap, $this->adDn, "(objectclass=user)") or die(json_encode(['success' => false, 'message' => "Error in search query: " . ldap_error($this->ldap)]));

                $result = ldap_get_entries($this->ldap, $query);
                $data = $this->utf8ize($this->cleanUpEntry($result));

                if(count($data) > 0){
                    $keys = array_keys($data);
                    $currentKey;

                    foreach ($keys as $key) {
                        if(strpos($key, $username) !== false){
                            $currentKey = $key;
                            break;
                        }
                    }

                    echo json_encode(['success' => true, 'data' => $data[$currentKey], 'dn' => $currentKey]);
                }else{
                    echo json_encode(['success' => false, 'message' => "Username & Password Salah"]);
                }
            }else{
                echo json_encode($result);
            }
        });
    }

    function add()
    {
        putenv('LDAPTLS_REQCERT=never');
        // $adServer = "ldaps://ad.nginovasi.id:636";
        $adServer = "ldap://ad.nginovasi.id:389";
        // $adServer = "ldap://202.157.185.66:389";
        // $adServer = "ldap://111.68.112.158:38900"; //new

        $ldap = ldap_connect($adServer);

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $name = $this->request->getPost('name');
        $email = $this->request->getPost('email');
        $group = $this->request->getPost('group');
        $arrName = explode(" ", $name);
        
        $sn = "OU=".$group.",".$this->adDn;

        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);

        if(ldap_start_tls($ldap)){
            $bind = @ldap_bind($ldap, 'umam', 'Semarang123!');

            if($bind){
                $dn_user="CN=".$username.",".$sn;
                $ldaprecord['cn'] = $username;
                $ldaprecord['givenName'] = chop($name, end($arrName));  
                $ldaprecord['sn'] = end($arrName);
                $ldaprecord['sAMAccountName'] = $username;
                $ldaprecord['UserPrincipalName'] = $username."@ad.nginovasi.id";
                $ldaprecord['displayName'] = $name;
                $ldaprecord['name'] = $name;
                $ldaprecord['UserAccountControl'] = "544";
                $ldaprecord['objectclass'][0] = 'top';
                $ldaprecord['objectclass'][1] = 'person';
                $ldaprecord['objectclass'][2] = 'organizationalPerson';
                $ldaprecord['objectclass'][3] = 'user';
                $ldaprecord['mail'] = $email;
                $ldaprecord['userPassword'] = base64_encode($password);

                $add = @ldap_add($ldap, $dn_user, $ldaprecord);

                if($add) {
                    echo "User successfully added";
                } else {
                    echo json_encode(array("error" => ldap_error($ldap), "event" => "on add user"));
                }
            }else{
                echo json_encode(["error" => ldap_error($ldap), "event" => "on binding"]);
            }
        }else{
            echo json_encode(["error" => ldap_error($ldap), "event" => "on tls"]);
        }

        @ldap_close($ldap);
    }

    function modify()
    {
        putenv('LDAPTLS_REQCERT=never');
        $adServer = "ldap://ad.nginovasi.id:389";
        // $adServer = "ldap://202.157.185.66:389";

        $ldap = ldap_connect($adServer);
        $username = $this->request->getPost('username');
        $password = base64_encode($this->request->getPost('password'));
        $sn = "OU=mahasiswa,DC=nginovasi,DC=id";

        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);

        $bind = @ldap_bind($ldap, 'umam', 'Semarang123!');
        
        if($bind){
            $dn_user='CN='.$username.','.$sn;

            $ldaprecord['userPassword'] = mb_convert_encoding('"'.$password.'"', "UTF-16LE");

            $modify = @ldap_modify($ldap, $dn_user, $ldaprecord);

            if($modify) {
                echo "User modified";
            } else {
                echo json_encode(["error" => ldap_error($ldap), "event" => "on modify"]);
            }
        }else{
            echo json_encode(["error" => ldap_error($ldap), "event" => "on binding"]);
        }

        @ldap_close($ldap);
    }

    function delete()
    {
        $adServer = "ldap://ad.nginovasi.id:389";
        // $adServer = "ldap://202.157.185.66:389";

        $ldap = ldap_connect($adServer);
        $username = $this->request->getPost('username');
        $group = $this->request->getPost('group') ?? "admin";
        $sn = "OU=".$group.",DC=nginovasi,DC=id";

        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);

        $bind = @ldap_bind($ldap, 'umam', 'Semarang123!');
        // $bind = @ldap_bind($ldap, 'umam', 'Semarang123!');

        $dn_user='CN='.$username.','.$sn;

        $delete = @ldap_delete($ldap, $dn_user);

        if($delete) {
            echo "User deleted";
        } else {
            echo json_encode(["dn" => $dn_user, "error" => ldap_error($ldap)]);
        }

        @ldap_close($ldap);
    }

    function all()
    {
        $data = $this->request->getPost();

        $this->_ldapBind(function($result) use ($data) {
            if($result['success']){
                $query = @ldap_search($this->ldap, $this->adDn, "(&(objectclass=user)(!(cn=*pws))(!(memberof=*)))") or die(json_encode(['success' => false, 'message' => "Error in search query: " . ldap_error($this->ldap)]));
                // $query = @ldap_search($this->ldap, $this->adDn, "(objectclass=user)") or die(json_encode(['success' => false, 'message' => "Error in search query: " . ldap_error($this->ldap)]));

                $result = ldap_get_entries($this->ldap, $query);
                $data = $this->utf8ize($this->cleanUpEntry($result));

                echo json_encode($data);
            }else{
                echo json_encode($result);
            }
        });
    }

    function add_bjt()
    {
        $username = $this->request->getPost('username');
        $name = $this->request->getPost('name');
        $email = $this->request->getPost('email');
        $department = $this->request->getPost('department');

        $adServer = "ldap://ad.nginovasi.id:389";
        $ldap = ldap_connect($adServer);
        
        $sn = "OU=userad,".$this->adDn;

        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);

        $bind = @ldap_bind($ldap, 'umam', 'Semarang123!');

        if($bind){
            $dn_user="CN=".$username.",".$sn;
            $ldaprecord['cn'] = $username;
            $ldaprecord['givenName'] = $name;  
            $ldaprecord['sn'] = "Bank Jateng";
            $ldaprecord['sAMAccountName'] = $username;
            $ldaprecord['UserPrincipalName'] = $username."@ad.nginovasi.id";
            $ldaprecord['displayName'] = $name." Bank Jateng";
            $ldaprecord['name'] = $name;
            $ldaprecord['UserAccountControl'] = "544";
            $ldaprecord['objectclass'][0] = 'top';
            $ldaprecord['objectclass'][1] = 'person';
            $ldaprecord['objectclass'][2] = 'organizationalPerson';
            $ldaprecord['objectclass'][3] = 'user';
            $ldaprecord['mail'] = $email;
            $ldaprecord['department'] = $department;
            $ldaprecord['userPassword'] = "123";

            $add = @ldap_add($ldap, $dn_user, $ldaprecord);

            if($add) {
                echo "User successfully added";
            } else {
                echo json_encode(array("error" => ldap_error($ldap), "event" => "on add user"));
            }
        }else{
            echo json_encode(["error" => ldap_error($ldap), "event" => "on binding"]);
        }

        @ldap_close($ldap);
    }

    function all_bjt()
    {
        $data = $this->request->getPost();

        $this->_ldapBind(function($result) use ($data) {
            if($result['success']){
                $query = @ldap_search($this->ldap, $this->adDn, "(cn=*pws*)") or die(json_encode(['success' => false, 'message' => "Error in search query: " . ldap_error($this->ldap)]));
                // $query = @ldap_search($this->ldap, $this->adDn, "(objectclass=user)") or die(json_encode(['success' => false, 'message' => "Error in search query: " . ldap_error($this->ldap)]));

                $result = ldap_get_entries($this->ldap, $query);
                $data = $this->utf8ize($this->cleanUpEntry($result));

                echo json_encode($data);
            }else{
                echo json_encode($result);
            }
        });
    }

    function ou()
    {
        $data = $this->request->getPost();

        $this->_ldapBind(function($result) use ($data) {
            if($result['success']){
                $query = @ldap_search($this->ldap, $this->adDn, "(&(objectClass=top)(|(objectClass=organizationalUnit)))") or die(json_encode(['success' => false, 'message' => "Error in search query: " . ldap_error($this->ldap)]));
                // $query = @ldap_search($this->ldap, $this->adDn, "(objectclass=user)") or die(json_encode(['success' => false, 'message' => "Error in search query: " . ldap_error($this->ldap)]));

                $result = ldap_get_entries($this->ldap, $query);
                $data = $this->utf8ize($this->cleanUpEntry($result));

                echo json_encode($data);
            }else{
                echo json_encode($result);
            }
        });
    }

    function modify_bjt()
    {   
        $username = $this->request->getPost('username');
        $department = $this->request->getPost('department');

        putenv('LDAPTLS_REQCERT=never');
        $adServer = "ldap://ad.nginovasi.id:389";
        // $adServer = "ldap://202.157.185.66:389";

        $ldap = ldap_connect($adServer);
        $username = $this->request->getPost('username');
        $password = base64_encode($this->request->getPost('password'));
        $sn = "OU=mahasiswa,DC=nginovasi,DC=id";

        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);

        $bind = @ldap_bind($ldap, 'umam', 'Semarang123!');
        
        if($bind){
            $dn_user='CN='.$username.',OU=userad,DC=nginovasi,DC=id';

            $ldaprecord['userPassword'] = '123';
            $ldaprecord['department'] = $department; //"D001DBK001";
            $ldaprecord['mail'] = $username.'@nginovasi.id';

            $modify = @ldap_modify($ldap, $dn_user, $ldaprecord);

            if($modify) {
                echo "User modified";
            } else {
                echo json_encode(["error" => ldap_error($ldap), "event" => "on modify"]);
            }
        }else{
            echo json_encode(["error" => ldap_error($ldap), "event" => "on binding"]);
        }

        @ldap_close($ldap);
    }

    function tes()
    {
        $data = $this->request->getPost();

        // $this->adServer = "ldap://111.68.31.157:389";
        // $this->adUser = "ngi_sync@akpol.ac.id";
        // $this->adPass = "NusantaraGlobalInovasi@2021";
        // $this->adDn = "DC=akpol,DC=ac,DC=id";

        $this->_ldapBind(function($result) use ($data) {
            if($result['success']){
                $query = @ldap_search($this->ldap, $this->adDn, "(&(objectClass=top)(|(objectClass=User)))") or die(json_encode(['success' => false, 'message' => "Error in search query: " . ldap_error($this->ldap)]));
                // $query = @ldap_search($this->ldap, $this->adDn, "(&(objectClass=top)(|(objectClass=organizationalUnit)))") or die(json_encode(['success' => false, 'message' => "Error in search query: " . ldap_error($this->ldap)]));

                $result = ldap_get_entries($this->ldap, $query);
                $data = $this->utf8ize($this->cleanUpEntry($result));

                echo json_encode($data);
            }else{
                echo json_encode($result);
            }
        });
    }

    function pendidik()
    {
        $data = $this->request->getPost();

        // $this->adServer = "ldap://111.68.31.157:389";
        // $this->adUser = "ngi_sync@akpol.ac.id";
        // $this->adPass = "NusantaraGlobalInovasi@2021";
        $this->adDn = "ou=pendidik,".$this->adDn;

        $this->_ldapBind(function($result) use ($data) {
            if($result['success']){
                $query = @ldap_search($this->ldap, $this->adDn, "(&(objectClass=top)(objectClass=User))") or die(json_encode(['success' => false, 'message' => "Error in search query: " . ldap_error($this->ldap)]));
                // $query = @ldap_search($this->ldap, $this->adDn, "(&(objectClass=top)(|(objectClass=organizationalUnit)))") or die(json_encode(['success' => false, 'message' => "Error in search query: " . ldap_error($this->ldap)]));

                $result = ldap_get_entries($this->ldap, $query);
                $data = $this->utf8ize($this->cleanUpEntry($result));

                echo json_encode($data);
            }else{
                echo json_encode($result);
            }
        });
    }

    function taruna()
    {
        $data = $this->request->getPost();

        // $this->adServer = "ldap://111.68.31.157:389";
        // $this->adUser = "ngi_sync@akpol.ac.id";
        // $this->adPass = "NusantaraGlobalInovasi@2021";
        $this->adDn = "ou=taruna,".$this->adDn;

        $this->_ldapBind(function($result) use ($data) {
            if($result['success']){
                $query = @ldap_search($this->ldap, $this->adDn, "(&(objectClass=top)(objectClass=User))") or die(json_encode(['success' => false, 'message' => "Error in search query: " . ldap_error($this->ldap)]));
                // $query = @ldap_search($this->ldap, $this->adDn, "(&(objectClass=top)(|(objectClass=organizationalUnit)))") or die(json_encode(['success' => false, 'message' => "Error in search query: " . ldap_error($this->ldap)]));

                $result = ldap_get_entries($this->ldap, $query);
                $data = $this->utf8ize($this->cleanUpEntry($result));

                echo json_encode($data);
            }else{
                echo json_encode($result);
            }
        });
    }

    function admin()
    {
        $data = $this->request->getPost();

        // $this->adServer = "ldap://111.68.31.157:389";
        // $this->adUser = "ngi_sync@akpol.ac.id";
        // $this->adPass = "NusantaraGlobalInovasi@2021";
        $this->adDn = "ou=admin,".$this->adDn;

        $this->_ldapBind(function($result) use ($data) {
            if($result['success']){
                $query = @ldap_search($this->ldap, $this->adDn, "(&(objectClass=top)(objectClass=User))") or die(json_encode(['success' => false, 'message' => "Error in search query: " . ldap_error($this->ldap)]));
                // $query = @ldap_search($this->ldap, $this->adDn, "(&(objectClass=top)(|(objectClass=organizationalUnit)))") or die(json_encode(['success' => false, 'message' => "Error in search query: " . ldap_error($this->ldap)]));

                $result = ldap_get_entries($this->ldap, $query);
                $data = $this->utf8ize($this->cleanUpEntry($result));

                echo json_encode($data);
            }else{
                echo json_encode($result);
            }
        });
    }

    function generatependidik(){
        $this->adDn = "ou=pendidik,".$this->adDn;

        $pendidik = $this->db->query('select a.nrp, a.namagadik, a.jab, b.username from m_user_pendidik a
                                        left join m_user b on b.id = a.id_m_user
                                        where b.username not like "%@%"')->getResult();

        if (!file_exists(APPPATH.'../writable/logs/log-insert.log')) {
            touch(APPPATH.'../writable/logs/log-insert.log');
        }else{
            $log = fopen(APPPATH.'../writable/logs/log-insert.log', "w") or die("Unable to open file!");

            $this->_ldapBind(function($result) use ($log, $pendidik) {
                if($result['success']){
                    foreach($pendidik as $pend){
                        $txt = $pend->nrp."\n";
                        $username = $pend->nrp;

                        $query = @ldap_search($this->ldap, $this->adDn, "(cn=$username)") or $txt .= "Error in search query: " . ldap_error($this->ldap) . "\n\n";
                        $result = ldap_get_entries($this->ldap, $query);
                        $data = $this->utf8ize($this->cleanUpEntry($result));

                        $ldaprecord['userPassword'] = base64_encode($username);

                        if(count($data) > 0){
                            $keys = array_keys($data);
                            $dn_user;

                            foreach ($keys as $key) {
                                if(strpos($key, $username) !== false){
                                    $dn_user = $key;
                                    break;
                                }
                            }

                            $modify = @ldap_modify($this->ldap, $dn_user, $ldaprecord);

                            if($modify) {
                                $txt .= json_encode(array("success" => true, "event" => "modify"))."\n\n";
                                fwrite($log, $txt); 
                            } else {
                                $txt .= json_encode(array("success" => false, "event" => "modify", "error" => ldap_error($this->ldap), "dn_user" => $dn_user))."\n\n";
                                fwrite($log, $txt); 
                            }
                        }else{
                            $username = $pend->nrp;
                            $name = $pend->namagadik;
                            $arrName = explode(" ", $name);
                            $dn_user="CN=".$username.",".$this->adDn;

                            $ldaprecord['cn'] = $username;
                            $ldaprecord['givenName'] = chop($name, end($arrName)) == "" ? end($arrName) : chop($name, end($arrName));  
                            $ldaprecord['sn'] = end($arrName);
                            $ldaprecord['sAMAccountName'] = substr($username, 0, 20);
                            $ldaprecord['UserPrincipalName'] = $username."@akpol.ac.id";
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
                                $txt .= json_encode(array("success" => true, "event" => "add"))."\n\n";
                                fwrite($log, $txt); 
                            } else {
                                $txt .= json_encode(array("success" => false, "message" => ldap_error($this->ldap), "event" => "add", "dn_user" => $dn_user))."\n\n";
                                fwrite($log, $txt); 
                            }
                        }
                    }
                }else{
                    $txt = "Error on bind\n\n";
                    fwrite($log, $txt);
                }
            });

            fclose($log);
        }

        echo "done";
    }

    function creatependidik(){
        $this->adDn = "ou=pendidik,".$this->adDn;

        $nrp = $this->request->getPost('nrp');
        $pend = $this->db->query('select a.nrp, a.namagadik, a.jab, b.username from m_user_pendidik a
                                        left join m_user b on b.id = a.id_m_user
                                        where b.username = "'.$nrp.'"')->getRow();

       $this->_ldapBind(function($result) use ($pend) {
            if($result['success']){
                $username = $pend->nrp;
                $query = @ldap_search($this->ldap, $this->adDn, "(cn=$username)") or die("Error in search query: " . ldap_error($this->ldap));
                $result = ldap_get_entries($this->ldap, $query);
                $data = $this->utf8ize($this->cleanUpEntry($result));

                $ldaprecord['userPassword'] = base64_encode($pend->nrp);

                if(count($data) > 0){
                    $keys = array_keys($data);
                    $dn_user;

                    foreach ($keys as $key) {
                        if(strpos($key, $username) !== false){
                            $dn_user = $key;
                            break;
                        }
                    }

                    $modify = @ldap_modify($this->ldap, $dn_user, $ldaprecord);

                    if($modify) {
                        echo json_encode(array("success" => true, "event" => "modify"));
                    } else {
                        echo json_encode(array("success" => false, "event" => "modify", "error" => ldap_error($this->ldap), "dn_user" => $dn_user));
                    }
                }else{
                    $username = $pend->nrp;
                    $name = $pend->namagadik;
                    $arrName = explode(" ", $name);
                    $dn_user="CN=".$username.",".$this->adDn;

                    $ldaprecord['cn'] = $username;
                    $ldaprecord['givenName'] = chop($name, end($arrName)) == "" ? end($arrName) : chop($name, end($arrName));  
                    $ldaprecord['sn'] = end($arrName);
                    $ldaprecord['sAMAccountName'] = substr($username, 0, 20);
                    $ldaprecord['UserPrincipalName'] = $username."@akpol.ac.id";
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
                        echo json_encode(array("success" => true, "event" => "add"));
                    } else {
                        echo json_encode(array("success" => false, "message" => ldap_error($this->ldap), "event" => "add", "dn_user" => $dn_user));
                    }
                }
            }else{
                echo "Error on bind";
            }
        });
    }

    function cekpendidik(){
        $this->adDn = "ou=pendidik,".$this->adDn;

        $pendidik = $this->db->query('select a.nrp, a.namagadik, a.jab, b.username from m_user_pendidik a
                                        left join m_user b on b.id = a.id_m_user
                                        where b.username not like "%@%"')->getResult();

        if (!file_exists(APPPATH.'../writable/logs/log-check.log')) {
            touch(APPPATH.'../writable/logs/log-check.log');
        }else{
            $log = fopen(APPPATH.'../writable/logs/log-check.log', "w") or die("Unable to open file!");

            $this->_ldapBind(function($result) use ($log, $pendidik) {
                if($result['success']){
                    foreach($pendidik as $pend){
                        $txt = $pend->nrp."\n";
                        $username = $pend->nrp;
                        $password = base64_encode($username);

                        $query = @ldap_search($this->ldap, $this->adDn, "(&(|(cn=$username)(mail=$username))(userPassword=$password))") or $txt .= "Error in search query: " . ldap_error($this->ldap) . "\n\n";
                        $result = ldap_get_entries($this->ldap, $query);
                        $data = $this->utf8ize($this->cleanUpEntry($result));

                        // if(json_encode($data) == "[]"){

                        //     $dn_user = "cn=".$username.",".$this->adDn;
                        //     $ldaprecord["userpassword"] = $password;

                        //     $modify = @ldap_modify($this->ldap, $dn_user, $ldaprecord);

                        //     if($modify) {
                        //         $txt .= json_encode(array("success" => true, "event" => "modify"))."\n\n";
                        //         fwrite($log, $txt); 
                        //     } else {
                        //         $txt .= json_encode(array("success" => false, "event" => "modify", "error" => ldap_error($this->ldap), "dn_user" => $dn_user))."\n\n";
                        //         fwrite($log, $txt); 
                        //     }
                        // }

                        if(json_encode($data) == "[]"){
                            $txt .= "--\n\n";
                            fwrite($log, $txt);
                        }else{
                            $txt .= "Login sukses\n\n";
                            fwrite($log, $txt);
                        }
                    }
                }else{
                    $txt = "Error on bind\n\n";
                    fwrite($log, $txt);
                }
            });

            fclose($log);
        }

        echo "done";
    }

    function cekadmin(){
        $this->adDn = "ou=admin,".$this->adDn;

        $pendidik = $this->db->query('select a.* from m_user a
                                    where a.type_code = "sad" and email like "%@akpol.ac.id"')->getResult();

        if (!file_exists(APPPATH.'../writable/logs/log-check.log')) {
            touch(APPPATH.'../writable/logs/log-check.log');
        }else{
            $log = fopen(APPPATH.'../writable/logs/log-check.log', "w") or die("Unable to open file!");

            $this->_ldapBind(function($result) use ($log, $pendidik) {
                if($result['success']){
                    foreach($pendidik as $pend){
                        $txt = $pend->username."\n";
                        $username = $pend->username;
                        $email = $pend->email;
                        $password = base64_encode($username);

                        $query = @ldap_search($this->ldap, $this->adDn, "(&(|(cn=$username)(mail=$username)(samaccountname=$username))(userPassword=$password))") or $txt .= "Error in search query: " . ldap_error($this->ldap) . "\n\n";
                        // $query = @ldap_search($this->ldap, $this->adDn, "(UserPrincipalName=$email)") or $txt .= "Error in search query: " . ldap_error($this->ldap) . "\n\n";
                        $result = ldap_get_entries($this->ldap, $query);
                        $data = $this->utf8ize($this->cleanUpEntry($result));

                        if(json_encode($data) == "[]"){
                            $txt .= "--\n\n";
                            fwrite($log, $txt);
                        }

                        // if(count($data) > 0){
                        //     $keys = array_keys($data);
                        //     $dn_user;

                        //     foreach ($keys as $key) {
                        //         $dn_user = $key;
                        //         break;
                        //     }

                        //     $ldaprecord["userpassword"] = $password;
                            
                        //     $modify = @ldap_modify($this->ldap, $dn_user, $ldaprecord);

                        //     if($modify) {
                        //         echo json_encode(array("success" => true, "event" => "modify"));
                        //     } else {
                        //         echo json_encode(array("success" => false, "event" => "modify", "error" => ldap_error($this->ldap), "dn_user" => $dn_user));
                        //     }
                        // }
                    }
                }else{
                    $txt = "Error on bind\n\n";
                    fwrite($log, $txt);
                }
            });

            fclose($log);
        }

        echo "done";
    }

    function cektaruna(){
        $this->adDn = "ou=taruna,".$this->adDn;

        $pendidik = $this->db->query('select b.username, c.tahun_masuk from m_user_taruna a
                                        inner join m_user b on a.id_m_user = b.id
                                        inner join m_sm_batalyon c on a.id_batalyon = c.id
                                        order by c.tahun_masuk desc')->getResult();

        if (!file_exists(APPPATH.'../writable/logs/log-check.log')) {
            touch(APPPATH.'../writable/logs/log-check.log');
        }else{
            $log = fopen(APPPATH.'../writable/logs/log-check.log', "w") or die("Unable to open file!");

            $this->_ldapBind(function($result) use ($log, $pendidik) {
                if($result['success']){
                    foreach($pendidik as $pend){
                        $txt = $pend->username."/ (".$pend->tahun_masuk.")\n";
                        $username = $pend->username;
                        $tahun = $pend->tahun_masuk;
                        $password = base64_encode($username);

                        $query = @ldap_search($this->ldap, $this->adDn, "(&(cn=$username)(userPassword=$password))") or $txt .= "Error in search query: " . ldap_error($this->ldap) . "\n\n";
                        $result = ldap_get_entries($this->ldap, $query);
                        $data = $this->utf8ize($this->cleanUpEntry($result));

                        if(json_encode($data) == "[]"){

                            $dn_user = "cn=".$username.",ou=".$tahun.",".$this->adDn;
                            $ldaprecord["userpassword"] = $password;

                            $modify = @ldap_modify($this->ldap, $dn_user, $ldaprecord);

                            if($modify) {
                                $txt .= json_encode(array("success" => true, "event" => "modify"))."\n\n";
                                fwrite($log, $txt); 
                            } else {
                                $txt .= json_encode(array("success" => false, "event" => "modify", "error" => ldap_error($this->ldap), "dn_user" => $dn_user))."\n\n";
                                fwrite($log, $txt); 
                            }
                        }else{
                            $txt .= "Login sukses\n\n";
                            fwrite($log, $txt);
                        }

                        // if(json_encode($data) == "[]"){
                        //     $txt .= "--\n\n";
                        //     fwrite($log, $txt);
                        // }else{
                        //     $txt .= "Login sukses\n\n";
                        //     fwrite($log, $txt);
                        // }
                    }
                }else{
                    $txt = "Error on bind\n\n";
                    fwrite($log, $txt);
                }
            });

            fclose($log);
        }

        echo "done";
    }


    // function tes()
    // {
    //     echo json_encode($_POST);
    // }
}