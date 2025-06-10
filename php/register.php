<?php
if(!isset($_POST)) 
{
    header("Location: ../index.html");
    exit();
    return;
}

header('Content-Type: application/json');
$response = array(
    'message' => 'Data received successfully',
    'status' => false
);

$EMAIL_REGEX = "/^((?!\.)[\w\-_.]*[^.])(@\w+)(\.\w+(\.\w+)?[^.\W])$/m";
$PHONE_NUMBER_REGEX = "/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/";
$LETTERS_ONLY_REGEX = "/^[a-zA-ZąćęłńóśźżĄĘŁŃÓŚŹŻ]+$/";
$SURNAME_REGEX = "/^[a-zA-ZąćęłńóśźżĄĘŁŃÓŚŹŻ\- ]+$/";
$NO_SPECIAL_SYMBOLS_REGEX = "/^[a-zA-Z0-9\s!@#$%^&*()_+\-=\[\]{}|:\\;,.<>\/?]*$/";

$email = $_POST['input-email'];
$number = str_replace(' ', '', $_POST['input-phone-number']);
$first_name = $_POST['input-first-name'];
$last_name = $_POST['input-last-name'];
$password = $_POST['input-password'];

$data = [$email, $number, $first_name, $last_name, $password];
$lengths = [64, 20, 32, 32, 64];
$regex = [$EMAIL_REGEX, $PHONE_NUMBER_REGEX, $LETTERS_ONLY_REGEX, $SURNAME_REGEX, $NO_SPECIAL_SYMBOLS_REGEX];

if (validate_form($data, $lengths, $regex) == false) $response['message'] = "Sent form is invalid";

if(userExists($email, $number) == true)
{
    $response['message'] = "User already exists.";
    echo json_encode($response);
    return;
}
else 
{
    $query = createuser($email, $number, $first_name, $last_name, $password);
    if($query['status'] == true)
    {
        $response['message'] = "User has been succesfully created.";
        $response['status'] = true;
    }
    else
    {
        $response['message'] = "Failed to create user";
    } 
}

echo json_encode($response);

function userExists($email, $number)
{
    $api_url = "https://filip.dev-code.pl/web/api/users/doesExist.php";

    $payload = json_encode(array(
        'email' => $email,
        'phone_number' => $number
    ));

    $ch = curl_init($api_url);

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($payload),
        'Authorization: Bearer APITOKEN'
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

    return $response_data['status'];
}

function createUser($email, $number, $first_name, $last_name, $password)
{
    $api_url = "https://filip.dev-code.pl/web/api/users/create.php";

    $payload = json_encode(array(
        'email' => $email,
        'phone_number' => $number,
        'first_name' => $first_name,
        'last_name' => $last_name,
        'password' => $password,    
        'dupa' => "dupa"
    ));

    $ch = curl_init($api_url);

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($payload),
        'Authorization: Bearer APITOKEN'
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

    return $response_data;
}

function get_curl($ch)
{
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    return curl_exec($ch);
}

function validate_form($variables, $lengths, $regex)
{
    $length = count($variables);
    for($i = 0; $i < $length; $i++)
    {
        if(strlen($variables[$i]) > $lengths[$i] || !preg_match($regex[$i], $variables[$i])) return false;
    }
    return true;
}
?>