<?php
require_once __DIR__ . '/../vendor/autoload.php';

//setup the config file
$config = new \AuthSDK\Config();
$config->setOrigin('digitalefactuur');

$tokenService = new \AuthSDK\TokenService(
    $config
);

// public key that is used in this example
$key = "-----BEGIN PUBLIC KEY-----
MFswDQYJKoZIhvcNAQEBBQADSgAwRwJAdXf35bq5zxYad+GZBi2UVK3GZg3l+RE+
+ICZ8pgUFpCD6PB8NlyIhmhyEoSDeWNDOw6g/MFdawcdzvbFK7dTMwIDAQAB
-----END PUBLIC KEY-----";

var_dump(
    $tokenService->verifyAccessToken(
        'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOjE2LCJyb2xlIjoiREVWIiwiaWF0IjoxNTIxNTUxMTg2LCJleHAiOjM5MzE1NTQ3ODYsImF1ZCI6WyJkaWdpdGFsZWZhY3R1dXIiXSwiaXNzIjoiQXV0aGVudGljYXRpb24gU2VydmVyIn0.CkeCIKPGWIqBRDPVkw91vg9Pw2loHEnwqYxiLYUWkP20D9G68HayeiUKCsI8XMyMiwTlz77ufOmDbgEaLyzBcQ',
        $key
    )
);


