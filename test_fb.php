<?php
$ch = curl_init('https://graph.facebook.com/v25.0/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
curl_setopt($ch, CURLOPT_VERBOSE, true);
$result = curl_exec($ch);
$err = curl_error($ch);
echo "Result: $result\n";
echo "Error: $err\n";
curl_close($ch);