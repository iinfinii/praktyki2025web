<?php
if ($_SERVER['REQUEST_METHOD'] != "POST") 
{
    http_response_code(405);
    echo json_encode(array("message" => "Method not allowed."));
    return;
}
    
require_once "../models/auth.php";
$api_token = "APITOKEN";

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods,Authorization,X-Requested-With");

include_once '../config/Database.php';
include_once '../models/Users.php';
include_once '../../php/Encryption.php';

$response = array(
    'message' => "Error when authorizing bearer token",
    'status' => false,
);
    
$authCode = getBearerToken();
if($authCode != $api_token) 
{
    http_response_code(401);
    $response = array("message" => "Unauthorized access.");
    echo json_encode($response);
    return;
}

$input = json_decode(file_get_contents("php://input"), true);
if(empty($input))
{
    http_response_code(400);
    $response['message'] = "No input data.";
    echo json_encode($response);
    return;
}

if(!array_key_exists('email', $input) || !array_key_exists('password', $input))
{
    http_response_code(406);
    $response['message'] = "Key values missing.";
    echo json_encode($response);
    return;
}

foreach($input as $key => $value)
{
    if(empty($value))
    {
        http_response_code(406);
        $response['message'] = "Incomplete dataset to login.";
        echo json_encode($response);
        return;
    }
}

$database = new Database();
$db = $database->getConnection();

$users = new Users($db);

$email = $input['email'];
$password = $input['password'];

$user = $users->login($email, $password);

if($user != null)
{
    $data = json_encode(array(
        'user_id' => $user['id'],
        'email' => $user['email'],
        'phone_number' => $user['phone_number'],
        'first_name' => $user['first_name'],
        'last_name' => $user['last_name'],
        'expiry_date' => time() + 3600
    ));

    $response['message'] = "Successfully logged in.";
    $response['status'] = true;
    $response['token'] = generate_token($data);

    http_response_code(200);
}
else
{
    $response['message'] = "Failed to login.";
    http_response_code(401);
}

function generate_token($data)
{
    $encryption_alg = new Encryption();
    $token = $encryption_alg->encrypt($data);
    
    return $token;
}

echo json_encode($response);
?>