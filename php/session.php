<?php
include_once "Encryption.php";
class Session
{
    public function start_session($data, $iv)
    {
        session_start();
        session_unset();

        $_SESSION['data'] = $data;
        $_SESSION['iv'] = $iv;
    }

    public function auth()
    {
        session_start();
        $response = array("message" => session_status(),
                                        "authorized" => false);

        if(!isset($_SESSION['data']) || !isset($_SESSION['iv'])) 
        {
            $response["message"] = "Session token or iv not found";
            return $response;
        }

        $encryption_alg = new Encryption();
        $data = $encryption_alg->decrypt($_SESSION['data'], $_SESSION['iv']);

        if($data === false)
        {
            $response['message'] = "Decryption failed";
            return $response;
        }

        foreach($data as $key => $value)
        {
            if(empty($value) || $value == "null") 
            {
                $response['message'] = "At least one of key values is empty";
                end_session();
                return $response;
            }
        }

        if($data['expiry_date'] < time())
        {
            $response['message'] = $decrypted_data;
            $this->end_session();
            return $response;
        }

        $response['message'] = "Session initialized";
        $response['authorized'] = true;
        $response['user_id'] = $data['data']['user_id'];

        return $response;
    }

    public function fetch()
    {
        session_start();
        $response = array("message" => session_status());

        if(!isset($_SESSION['data']) || !isset($_SESSION['iv'])) 
        {
            $response["message"] = "Session token or iv not found";
            return $response;
        }

        $encryption_alg = new Encryption();
        $data = $encryption_alg->decrypt($_SESSION['data'], $_SESSION['iv']);

        if($data === false)
        {
            $response['message'] = "Decryption failed";
            return $response;
        }

        foreach($data as $key => $value)
        {
            if(empty($value) || $value == "null") 
            {
                $response['message'] = "At least one of key values is empty";
                end_session();
                return $response;
            }
        }

        if($data['expiry_date'] < time())
        {
            $response['message'] = "Session expired";
            $this->end_session();
            return $response;
        }

        $response['message'] = "Success";
        $response['data'] = $data;

        return $response;
    }

    public function cust_auth($data, $iv)
    {
        $response = array("message" => "initialized",
                                        "authorized" => false);

                            $encryption_alg = new Encryption();

        $decrypted_data = $encryption_alg->decrypt($data, $iv);

        if($decrypted_data === false)
        {
            $response['message'] = "Decryption failed" . ", " . $data . ", " . $iv;;
            return $response;
        }

        foreach($decrypted_data as $key => $value)
        {
            if(empty($value) || $value == "null") 
            {
                $response['message'] = "At least one of key values is empty";
                return $response;
            }
        }

        if($decrypted_data['expiry_date'] < time())
        {
            $response['message'] = $decrypted_data . ", " . $data . ", " . $iv;
            return $response;
        }

        $response['message'] = "Session initialized";
        $response['authorized'] = true;
        $response['data'] = $decrypted_data;

        return $response;         
    }

    public function cust_fetch($data, $iv)
    {
        session_start();
        $response = array("message" => session_status(),
                                        "authorized" => false);

    
        $encryption_alg = new Encryption();

        $data = $encryption_alg->decrypt($data, $iv);

        if($decrypted_data === false)
        {
            $response['message'] = "Decryption failed";
            return $response;
        }

        foreach($data as $key => $value)
        {
            if(empty($value) || $value == "null") 
            {
                $response['message'] = "At least one of key values is empty";
                end_session();
                return $response;
            }
        }

        $data = $encryption_alg->decrypt($data, $iv);

        if($decrypted_data === false)
        {
            $response['message'] = "Decryption failed";
            return $response;
        }

        foreach($data as $key => $value)
        {
            if(empty($value) || $value == "null") 
            {
                $response['message'] = "At least one of key values is empty";
                end_session();
                return $response;
            }
        }

        if($data['expiry_date'] < time())
        {
            $response['message'] = "Session expired";
            $this->end_session();
            return $response;
        }

        $response['message'] = "Success";
        $response['data'] = $data;

        return $response;         
    }

    public function end_session()
    {
        session_start();
        session_unset();
        session_destroy();
    }
}

header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] == "GET")
{
    $session = new Session();
    
    echo json_encode($session->auth());
}

if($_SERVER['REQUEST_METHOD'] == "CUST_GET")
{
    $session = new Session();

    $payload = file_get_contents('php://input');
    $json = json_decode($payload, true);

    $data = $json["data"];
    $iv = $json["iv"];
    
    echo json_encode($session->cust_auth($data, $iv));
}

if($_SERVER['REQUEST_METHOD'] == "FETCH")
{
    $session = new Session();

    echo json_encode($session->fetch());
}

if($_SERVER['REQUEST_METHOD'] == "CUST_FETCH")
{
    $session = new Session();

    $payload = file_get_contents('php://input');
    $json = json_decode($jsonPayload, true);

    $data = $json["data"];
    $iv = $json["iv"];
    
    echo json_encode($session->cust_fetch($data, $iv));
}

if($_SERVER['REQUEST_METHOD'] == "LOGOUT")
{
    $session = new Session();
    $session->end_session();
    echo json_encode(array("message" => "User logged out"));
}

if($_SERVER['REQUEST_METHOD'] == "DELETE")
{
    $response = array("message" => "null");

    parse_str(file_get_contents("php://input"), $input);
    $password = $input['password'] ?? null;

    if($password == null || empty($password))
    {
        $response['message'] = "No password provided";
        echo json_encode($response);
        return;
    }

    $session = new Session();

    $user_data = $session->fetch();
    $user_email = $user_data['data']['email'];

    $api_url = "https://filip.dev-code.pl/web/api/users/tryLogin.php";

    $payload = json_encode(array(
        'email' => $user_email,
        'password' => $password
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

    $curl_data = $response_data;

    if($curl_data['status'] == true)
    {
        $api_url = "https://filip.dev-code.pl/web/api/users/delete.php";

        $payload = json_encode(array(
            'email' => $user_email,
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

        if($response_data['status'] == true)
        {
            $response['message'] = "User deleted.";
            $session = new Session();
            $session->end_session();
        }
        else
        {
            $response['message'] = "Failed to delete user.";
        }
    }
    else
    {
        $response['message'] = "Password invalid.";
    }

    echo json_encode($response);
}
?>