<?php
class Token
{
    private $encryption_key = "encryptionkey";
    private $cipher_method = 'aes-256-cbc';

    public function generate_token($id, $expiry_date)
    {
        $token = array(
            "data" => $id,
            "expiry_date" => $expiry_date
        );

        return $this->encrypt($id);
    }

    public function decrypt_token($encrypted_token, $iv)
    {
        $initialization_vector = base64_decode($iv);
        $encrypted_token = json_encode($encrypted_token);
        $decrypted_data = openssl_decrypt($encrypted_token, $this->cipher_method, $this->encryption_key, 0, $initialization_vector);

        return $decrypted_data === false ? "Decryption failed" : json_decode($decrypted_data, true);
    }

    private function encrypt($id)
    {
        $initialization_vector = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->cipher_method));
        $encrypted_data = openssl_encrypt($token, $this->cipher_method, $this->encryption_key, 0, $initialization_vector);

        return json_encode(array(
            'data' => $encrypted_data,
            'iv' => base64_encode($initialization_vector)
        ));
    }
}
?>