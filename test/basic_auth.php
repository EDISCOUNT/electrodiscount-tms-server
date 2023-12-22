<?php

$key = "ck_690ae76c426250303e810f74e60dece2f3b1bf77";
$secret = "cs_c95f8d8610994d6b3e48403efe62b184c41d60a6";

$basic = base64_encode($key . ':' . $secret);
echo $basic;