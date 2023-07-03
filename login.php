<?php

require "vendor/autoload.php";


$conn = API\Database::getInstance();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reqDatas = file_get_contents("php://input");
    $reqBody = json_decode($reqDatas, true);
   

    $result = $conn->performQuery("SELECT * FROM users WHERE email = ? ",[$reqBody["email"]]);

    $hashedPassword = $result["result"][0]["password"];
    
    if (password_verify($reqBody["password"],$hashedPassword)) {
        echo json_encode(array_merge(["code" => "200" , "status" => "success"],$result["result"][0]));
    }else {
        echo json_encode(["code" => "200" ,"status" => "Failed"]);
    }
}