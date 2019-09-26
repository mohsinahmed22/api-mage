    <?php
/**
 * Created by PhpStorm.
 * User: Mohsin
 * Page: Getting Data from Magento 2 updating to Mysql table.
 * Date: 9/24/2019
 * Time: 12:21 PM
 */
    include_once '../config/Database.php';

    // instantiate product object
    include_once '../objects/Prod.php';


    $database = new Database();
    $db = $database->getConnection();

    $product = new Prod($db);




$curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "http://192.168.100.7:8080/mage229/rest/default/V1/products?searchCriteria[filterGroups][0][filters][0][field]=sku&searchCriteria[pageSize]=100&searchCriteria[currentPage]=1&searchCriteria[filterGroups][0][filters][0][condition_type]=gt&searchCriteria[filterGroups][0][filters][0][value]=0",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 600,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_POSTFIELDS => "{\n\t\"stockItem\":{\n\t\t\"qty\":350\n\t\t\n\t}\n}",
        CURLOPT_HTTPHEADER => array(
            "Accept: */*",
            "Accept-Encoding: gzip, deflate",
            "Authorization: Bearer t7ddotqcyptammcnk0w54tshwg3dcmam",
            "Cache-Control: no-cache",
            "Connection: keep-alive",
            "Content-Length: 36",
            "Content-Type: application/json",
            "Cookie: PHPSESSID=67ip07qamjbo9hfk2sb4qhs7p6",
            "Host: beta1021.walkeaze.com",
            "Postman-Token: de469276-ec4b-4d56-87ed-6e81b3914caa,476563d4-51cb-4510-b442-2ceb6f011206",
            "User-Agent: PostmanRuntime/7.17.1",
            "cache-control: no-cache"
        ),
    ));

//curl_setopt_array($curl, array(
////  CURLOPT_PORT => "8080",
////  CURLOPT_URL => "http://192.168.100.7:8080/mage229/rest/default/V1/products?fields=sku,extension_attributes[stock_item[qty]]",
//  CURLOPT_URL => "http://beta1021.walkeaze.com/rest//default/V1/products?searchCriteria[filterGroups][0][filters][0][field]=sku&searchCriteria[filterGroups][0][filters][0][value]=0&searchCriteria[filterGroups][0][filters][0][condition_type]=gt&searchCriteria[pageSize]=3500&searchCriteria[currentPage]=1",
//  CURLOPT_RETURNTRANSFER => true,
//  CURLOPT_ENCODING => "",
//  CURLOPT_MAXREDIRS => 10,
//  CURLOPT_TIMEOUT => 5000,
//  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//  CURLOPT_CUSTOMREQUEST => "GET",
//  CURLOPT_POSTFIELDS => "{\n\t\"stockItem\":{\n\t\t\"qty\":350\n\t\t\n\t}\n}",
//  CURLOPT_HTTPHEADER => array(
//    "Accept: */*",
//    "Accept-Encoding: gzip, deflate",
//    "Authorization: Bearer q9si0fat54g3x3ellq3yayjl5khga8ei",
//    "Cache-Control: no-cache",
//    "Connection: keep-alive",
//    "Content-Length: 36",
//    "Content-Type: application/json",
//    "Cookie: PHPSESSID=cbkpjdt5ahgmr7ss1hq7nrknsl; private_content_version=f2b3f12ac80dadcf8eb74f0553975aa0",
//    "Host: 192.168.100.7:8080",
//    "Postman-Token: d09941ca-0d20-4f7f-93f5-03f26e7d07de,ec2acb7e-1afa-414a-b142-4ebda2491434",
//    "User-Agent: PostmanRuntime/7.17.1",
//    "cache-control: no-cache"
//  ),
//));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
echo $response;
    $res = json_decode($response);

    $count = 0;
    $array = [];
    foreach($res->items as $r){
        $array["items"][$count] = [
            "sku" =>  $r->sku,
            "qty" => 1,
            "item_id" => $r->id
        ];

        $count++;
    };

//    print_r($array["items"]);
    $object = json_encode($array["items"]);

    // get posted data
    $data = json_decode($object);
//    $data = json_decode(file_get_contents("php://input"));
//    print_r($data);
    // make sure data is not empty

    foreach($data as $d){

        if (
            !empty($d->sku) &&
            !empty($d->qty) &&
            !empty($d->item_id)
        ) {

            // set product property values
            $product->sku = $d->sku;
            $product->qty = $d->qty;
            $product->item_id = $d->item_id;
            // create the product
            if ($product->create()) {

                // set response code - 201 created
                http_response_code(201);

                // tell the user
                echo json_encode(array("message" => "Product was created."));
            } // if unable to create the product, tell the user
            else {

                // set response code - 503 service unavailable
                http_response_code(503);

                // tell the user
                echo json_encode(array("message" => "Unable to create product."));
            }
        } // tell the user data is incomplete
        else {

            // set response code - 400 bad request
            http_response_code(400);

            // tell the user
            echo json_encode(array("message" => "Unable to create product. Data is incomplete."));
        }
    }
}