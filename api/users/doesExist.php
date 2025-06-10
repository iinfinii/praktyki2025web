<?php
if ($_SERVER['REQUEST_METHOD'] != "POST") 
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
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods,Authorization,X-Requested-With");

include_once '../config/Database.php';
include_once '../models/Users.php';

$response = array(
    'message' => "Failed to get user.",
    'status' => true,
);

$input = json_decode(file_get_contents("php://input"), true);
if(empty($input))
{
    http_response_code(400);
    $response['message'] = "No input data.";
    echo json_encode($response);
    return;
}

if (empty($input['email']) || empty($input['phone_number']))
{
    http_response_code(406);

    $response['message'] = "Incomplete dataset to check user.";
    
    echo json_encode($response);
    return;
}

$database = new Database();
$db = $database->getConnection();

$users = new Users($db);
$email = $input['email'];
$phone_number = $input['phone_number'];

$result = $users->doesExist($email, $phone_number);

if (!$result)
{
    $response['message'] = "User doesn't exist.";
    $response['status'] = false;
} else {
    $response['message'] = "User already exists.";
}

http_response_code(200);

echo json_encode($response);
?>