<?php

// Security features

function encrypt($data, $key) : string {
    $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
    $iv = openssl_random_pseudo_bytes($ivlen);
    $ciphertext_raw = openssl_encrypt($data, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
    $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
    $ciphertext = base64_encode( $iv.$hmac.$ciphertext_raw );

    return $ciphertext;
}

function decrypt($data, $key) : ?string {
    $c = base64_decode($data);
    $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
    $iv = substr($c, 0, $ivlen);
    $hmac = substr($c, $ivlen, $sha2len=32);
    $ciphertext_raw = substr($c, $ivlen+$sha2len);
    $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
    $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);

    // timing attack safe comparison
    if (hash_equals($hmac, $calcmac)) {
        return $original_plaintext;
    }

    return null;
}

function getStaticKey($seed) : string {
    srand(crc32($seed));

    $buffer = "";
    for ($i = 0; $i < 64; $i++) {
        $buffer = $buffer . chr(rand(0, 127));
    }

    $buffer = sha1(base64_encode($buffer));

    return "HexStresser:key:" . $buffer;
}