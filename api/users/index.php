<?php
if ($_SERVER['REQUEST_METHOD'] != "GET") 
{
    http_response_code(405);
    echo json_encode(array("message" => "Method not allowed."));
    return;
}
require_once "../models/auth.php";
$api_token = "APITOKEN";

$authCode = getBearerToken();
if($authCode != $api_token) 
{
    http_response_code(401);
    $response = array("message" => "unauthorized.");
    echo json_encode($response);
    return;
}

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods,Authorization,X-Requested-With");

include_once '../config/Database.php';
include_once '../models/Users.php';

$database = new Database();
$db = $database->getConnection();

$users = new Users($db);

$result = $users->read();
$rows =  $result->rowCount();

if($rows > 0)
{
    $users_arr = array();
    $users_arr["users"] = array();

    while ($row = $result->fetch(PDO::FETCH_ASSOC)) 
    {
        extract($row);

        $user = array(
            "id" => $id,
            "email" => $email,
            "phone_number" => $phone_number,
            "first_name" => $first_name,
            "last_name" => $last_name,
        );
    
        array_push($users_arr["users"], $user);
    }
    http_response_code(200);
    echo json_encode($users_arr);
}  
else 
{
    http_response_code(404);
    echo json_encode(array("message" => "No users found."));
}
?>