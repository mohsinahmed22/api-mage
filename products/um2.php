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

    $auth = $product->connect('admin200',"Tmt123456");

    $stmt = $product->read();
    $num = $stmt->rowCount();


    // check if more than 0 record found
    if($num>0){

        // products array
        $products_arr=array();
        $products_arr["items"]=array();

        // retrieve our table contents
        // fetch() is faster than fetchAll()
        // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            // extract row
            // this will make $row['name'] to
            // just $name only
            extract($row);

            $product_item=array(
                "sku" => html_entity_decode($sku),
                "qty" => $qty,
                "item_id" => $item_id,
            );

            array_push($products_arr["items"], $product_item);
        }

        // set response code - 200 OK
        http_response_code(200);

        // show products data in json format
//        $data = json_encode($products_arr);
        $data = json_decode(json_encode($products_arr['items']), FALSE);


        foreach ($data as $d){
//            echo $d->sku . "<br/>";
//            echo "http://192.168.100.7:8080/mage229/rest/default/V1/products/$d->sku/stockItems/$d->item_id";
//            echo "{\r\n  \t\"stockItem\":{\r\n\t\t\"qty\":$d->qty\r\n\t\t\r\n\t}\r\n}" . "<br/><br/>";

            $data = array(
                "product" => array(
                    'extension_attributes'             => array(
                        'stockItem'             => array(
                            'qty' => $d->qty
                        )
                    )
                )
            );

            $retour = $product->put("products/".$d->sku, $data);

            print_r($retour);
            echo "<br/><br/>";


        }



    }else{

        // set response code - 404 Not found
        http_response_code(404);

        // tell the user no products found
        echo json_encode(
            array("message" => "No products found.")
        );
    }


