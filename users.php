<?php


require "vendor/autoload.php";

$reqMethod = $_SERVER['REQUEST_METHOD'];

if (isset($_GET["id"])){
    $id = $_GET["id"]; 
}

$reqBody = file_get_contents("php://input");

$conn = API\Database::getInstance();


if ($reqMethod == 'POST' && isset($reqBody)) {

    $reqBody = json_decode($reqBody,true);

    print_r($reqBody);
    $result = $conn->performQuery("INSERT INTO users (name,surname,email,password) VALUES (? , ? , ? , ?)",[$reqBody["name"],$reqBody["surname"],$reqBody["email"],password_hash($reqBody["password"],PASSWORD_DEFAULT)]);

}else if ($reqMethod == 'GET' && isset($id)){

    $result = $conn->performQuery("SELECT * FROM users WHERE id = ?",[$id]);
    


}else if ($reqMethod == 'GET' && !isset($id)){
    
    $result = $conn->performQuery("SELECT * FROM users",null);
}

    
if ($result["status"] === 'success'){
    $result = array_merge(["code" => "200"],$result);
}else if ($result["status"] === 'error'){
    $result = array_merge(["code" => "500"],$result);
}


echo json_encode($result);



