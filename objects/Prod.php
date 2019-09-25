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




//    Construct when initialize this class;

    public function __construct($db)
    {
        $this->conn = $db;
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

        $curl = curl_init();
//        curl_setopt_array($curl, array(
//            CURLOPT_PORT => "8080",
//            CURLOPT_URL => "http://192.168.100.7:8080/mage229/rest/default/V1/products/" . $sku . "/stockItems/". $item_id,
//            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_ENCODING => "",
//            CURLOPT_MAXREDIRS => 10,
//            CURLOPT_TIMEOUT => 500,
//            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//            CURLOPT_CUSTOMREQUEST => "PUT",
//            CURLOPT_POSTFIELDS => "{\r\n  \t\"stockItem\":{\r\n\t\t\"qty\":" . $qty  . "\r\n\t\t\r\n\t}\r\n}",
//            CURLOPT_HTTPHEADER => array(
//                "Accept: */*",
//                "Accept-Encoding: gzip, deflate",
//                "Authorization: Bearer cxk3myzl3i0u5q9a13cxz9b3cd5ke6pb",
//                "Cache-Control: no-cache",
//                "Connection: keep-alive",
//                "Content-Length: 43",
//                "Content-Type: application/json",
//                "Cookie: PHPSESSID=auo2tsb9b21nour9n01trtket4; private_content_version=f2b3f12ac80dadcf8eb74f0553975aa0",
//                "Host: 192.168.100.7:8080",
//                "Postman-Token: 3f206432-6ec4-4753-8a4b-0e415ee9ad8b,d51ba645-dffa-4c4c-af1e-5e8bc2edaf8e",
//                "User-Agent: PostmanRuntime/7.17.1",
//                "cache-control: no-cache"
//            ),
//        ));

        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://beta1021.walkeaze.com/rest/default/V1/products/36302S-Mules/stockItems/1",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS => "{\n\"stockItem\":{\n\"qty\":350\n}\n}",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "cache-control: no-cache"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);


        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            return $response;
        }

    }
}