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
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods,Authorization,X-Requested-With");

include_once '../config/Database.php';
include_once '../models/Users.php';

$response = array(
    'message' => "Failed to delete user.",
    'status' => false,
);

$input = json_decode(file_get_contents("php://input"), true);
if(empty($input))
{
    http_response_code(400);
    $response['message'] = "No input data.";
    echo json_encode($response);
    return;
}

if($input['email'] == null || empty($input['email']))
{
    http_response_code(406);
    $response['message'] = "No email provided.";
    echo json_encode($response);
    return;
}

$database = new Database();
$db = $database->getConnection();

$user = new Users($db);

if ($user->delete($input['email'])) 
{
    $response['message'] = "User has been succesfully deleted.";
    $response['status'] = true;
    http_response_code(200);
} 
else 
{
    $response['message'] = "Failed to delete user.";
    http_response_code(500);
}

echo json_encode($response);
?>