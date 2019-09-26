<?php
/**
 * Created by PhpStorm.
 * User: Mohsin
 * Date: 9/24/2019
 * Time: 11:50 AM
 */

class Prod
{
    // database connection and table name
    private $conn;
    private $table_name = "prod";


    //  Object Properties
    public $id;
    public $sku;
    public $qty;
    public $item_id;
    public $host = "http://192.168.100.7:8080/mage229";


    public  $url;
    public  $token;
    public  $user;
    public  $password;
    public  $headers;




//    Construct when initialize this class;

    public function __construct($db)
    {
        $this->conn = $db;
//        $this->auth = $this->auth();

        $this->url =  $this->host."/rest/V1/";
    }


    function read(){

        // select all query
        $query = "SELECT
                p.id, p.sku, p.qty, p.item_id
            FROM
                " . $this->table_name . " p";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // execute query
        $stmt->execute();

        return $stmt;
    }





    // create product
    function create(){

        // query to insert record
        $query = "INSERT INTO
                " . $this->table_name . "
            SET
                sku=:sku, qty=:qty, item_id=:item_id";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->sku=htmlspecialchars(strip_tags($this->sku));
        $this->qty=htmlspecialchars(strip_tags($this->qty));
        $this->item_id=htmlspecialchars(strip_tags($this->item_id));

        // bind values
        $stmt->bindParam(":sku", $this->sku);
        $stmt->bindParam(":qty", $this->qty);
        $stmt->bindParam(":item_id", $this->item_id);

        // execute query
        if($stmt->execute()){
            return true;
        }

        return false;

    }




    public function apicall($sku, $qty, $item_id){

        $this->auth = $string = str_replace('"', '', $this->auth()); ;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_PORT => "8080",
            CURLOPT_URL => "http://192.168.100.7:8080/mage229/rest/default/V1/products/" . $sku . "/stockItems/". $item_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 500,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS => "{\r\n  \t\"stockItem\":{\r\n\t\t\"qty\":" . $qty  . "\r\n\t\t\r\n\t}\r\n}",
            CURLOPT_HTTPHEADER => array(
                "Accept: */*",
                "Accept-Encoding: gzip, deflate",
                "Authorization: Bearer " . $this->auth,
                "Cache-Control: no-cache",
                "Connection: keep-alive",
                "Content-Length: 43",
                "Content-Type: application/json",
                "Cookie: PHPSESSID=auo2tsb9b21nour9n01trtket4; private_content_version=f2b3f12ac80dadcf8eb74f0553975aa0",
                "Host: 192.168.100.7:8080",
                "Postman-Token: 3f206432-6ec4-4753-8a4b-0e415ee9ad8b,d51ba645-dffa-4c4c-af1e-5e8bc2edaf8e",
                "User-Agent: PostmanRuntime/7.17.1",
                "cache-control: no-cache"
            ),
        ));

//        echo $this->host . "/default/V1/products/". $sku . "/stockItem/". $item_id;

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);


        if ($err) {
            echo "cURL Error #:" . $err . "<br/>";
        } else {
            return $response;
        }

    }

    public function connect($theuser, $thepass)
    {
        $this->user =       $theuser;
        $this->password =   $thepass;

        $data_string =      json_encode(array("username" => $this->user, "password" => $this->password));
        $ch =               curl_init($this->url.'integration/admin/token');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string))
        );
        $this->token =      json_decode(curl_exec($ch));
        $this->headers =    array("Authorization: Bearer ".$this->token);
    }

    public function get($theurl, $thesearch="")
    {
        if ($thesearch != "")
        {
            $temp = "";
            $iter = 0;

            foreach ($thesearch as $value)
            {
                $temp .= "searchCriteria[filter_groups][0][filters][$iter][field]=".$value[0]."&";
                $temp .= "searchCriteria[filter_groups][0][filters][$iter][value]=".$value[2]."&";
                $temp .= "searchCriteria[filter_groups][0][filters][$iter][condition_type]=".$value[1]."&";

                $iter++;
            }

            $temp = trim($temp, "&");

            $ch = curl_init($this->url.$theurl."?".$temp);
        }
        else
            $ch = curl_init($this->url.$theurl);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = json_decode(curl_exec($ch));
        return $result;
    }

    public function getValue($theurl, $thevalue1="", $thevalue2="", $thevalue3="")
    {
        $ch = curl_init($this->url.$theurl);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = (array)json_decode(curl_exec($ch));
        if ($thevalue3 != "")
            return $result["$thevalue1"]["$thevalue2"]["$thevalue3"];
        elseif ($thevalue2 != "")
            return $result["$thevalue1"]["$thevalue2"];
        elseif ($thevalue1 != "")
            return $result["$thevalue1"];
        else
            return $result;
    }


    public function post($theurl, $thedata)
    {
        $productData = json_encode($thedata);
        $ch = curl_init($this->url.$theurl);

        $setHeaders = array('Content-Type:application/json','Authorization: Bearer '.$this->token);

        curl_setopt($ch,CURLOPT_POSTFIELDS, $productData);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $setHeaders);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = json_decode(curl_exec($ch));
        return $result;
    }

    public function put($theurl, $thedata)
    {
        $productData = json_encode($thedata);
        $ch = curl_init($this->url.$theurl);

        $setHeaders = array('Content-Type:application/json','Authorization: Bearer '.$this->token);

        curl_setopt($ch,CURLOPT_POSTFIELDS, $productData);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $setHeaders);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = json_decode(curl_exec($ch));
        return $result;
    }

    public function delete($theurl)
    {
        //change space to %20 for url
        $theurl = str_replace(" ", "%20", $theurl);

        $ch = curl_init($this->url.$theurl);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = json_decode(curl_exec($ch));
        return $result;
    }

    public function token()
    {
        return $this->token;
    }

    public function url()
    {
        return $this->url;
    }
}