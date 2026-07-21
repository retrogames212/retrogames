<?php
$users = json_decode(file_get_contents('users.json'), true);
$order_id = $_POST['order_id'];
foreach ($users as $u => $d) {
    if ($d['order_id'] == $order_id) {
        echo "Your Account: <br>User: $u <br>Pass: {$d['pass']}";
        die();
    }
}
echo "The Order ID has not been processed or is incorrect..";
?>