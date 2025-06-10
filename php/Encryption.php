<?php
class Encryption
{
    private $encryption_key = "encryptionkey";
    private $cipher_method = 'aes-256-cbc';
    
    public function encrypt($data)
    {
        $initialization_vector = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->cipher_method));
        $encrypted_data = openssl_encrypt($data, $this->cipher_method, $this->encryption_key, 0, $initialization_vector);
    
        return json_encode(array(
            'data' => base64_encode($encrypted_data),
            'iv' => base64_encode($initialization_vector)
        ));
    }

    public function decrypt($encrypted_data, $iv)
    {
        $initialization_vector = base64_decode($iv);
        $decoded_data = base64_decode($encrypted_data);
        $decrypted_data = openssl_decrypt($decoded_data, $this->cipher_method, $this->encryption_key, 0, $initialization_vector);

        return $decrypted_data === false ? false : json_decode($decrypted_data, true);
    }
}
?>