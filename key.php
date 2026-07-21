<?php

$ipn_secret = "pSn6U6YdfPEn60oiPrEHWzJeKlLMGSRQ";
$raw_data = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_NOWPAYMENTS_SIG'];

if ($signature == hash_hmac('sha512', $raw_data, $ipn_secret)) {
    $data = json_decode($raw_data, true);
    if ($data['payment_status'] == 'finished') {
        $order_id = $data['order_id'];
        $users = file_exists('users.json') ? json_decode(file_get_contents('users.json'), true) : [];
        
        $new_user = "user_" . substr(md5(rand()), 0, 6);
        $new_pass = substr(md5(rand()), 0, 8);
        
        $users[$new_user] = ['pass' => $new_pass, 'order_id' => $order_id];
        file_put_contents('users.json', json_encode($users));
    }
}
?>
