<?php
if(!isset($_POST)) 
{
    header("Location: ./index.html");
    exit();
    return;
}
header('Content-Type: application/json');
$response = array(
    'message' => 'Failed to log in',
    'status' => 'false'
);
include_once "session.php";
include_once "Encryption.php";

$email = $_POST['input-email'];
$password = $_POST['input-password'];

$NO_SPECIAL_SYMBOLS_REGEX = "/^[a-zA-Z0-9\s!@#$%^&*()_+\-=\[\]{}|:\\;,.<>\/?]*$/";

if(!preg_match($NO_SPECIAL_SYMBOLS_REGEX, $email) || !preg_match($NO_SPECIAL_SYMBOLS_REGEX, $password) 
    || strlen($email) > 64 || strlen($password) > 64) 
{
    $response['message'] = "Data invalid";
    echo json_encode($response);
    return;
}    

$api_url = "https://filip.dev-code.pl/web/api/users/tryLogin.php";

$payload = json_encode(array(
    'email' => $email,
    'password' => $password
));

$ch = curl_init($api_url);

curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($payload),
    'Authorization: Bearer 3cVtGKWGBertorrUaootbzlh8dIXBrWp2mER7kscH3FCRkvCWm1I8Z1BsMEl0xqL'
));

$curl_response = curl_exec($ch);

if (curl_errno($ch)) {
    curl_close($ch);
    throw new Exception("API Request Error: " . curl_error($ch));
}

curl_close($ch);

$response_data = json_decode($curl_response, true);

if (!is_array($response_data)) {
    throw new Exception("Invalid API Response: " . $curl_response);
}

$curl_data = $response_data;

if($curl_data['status'] == true)
{
    $curl_token = json_decode($curl_data['token'], true);

    $session = new Session();
    $session->start_session($curl_token['data'], $curl_token['iv']);
    $encryption_alg = new Encryption();
    $response['message'] = "Logged in successfully";
    $response['status'] = true;
}
else
{
    $response['message'] = $curl_response;
}

echo json_encode($response);
?>