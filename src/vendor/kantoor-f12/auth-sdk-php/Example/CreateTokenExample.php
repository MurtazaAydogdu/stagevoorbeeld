<?php
require_once __DIR__ . '/../vendor/autoload.php';

$privatekey = '-----BEGIN RSA PRIVATE KEY-----
MIIBOwIBAAJBANOjP3Mafsgg5lR8oIQiueTiHE+gb/NEHjI4roXKgrQi9QT7/dIo
LZMdQGlBs4t1GBaUVUfactvTXspURUL/ltsCAwEAAQJBAJC/C0tXKLUJw16vaj1V
XtzLRN+09DxmL1zb5Yk960z1iPDsYY92N9y7HG1RnFCite7pNq1NBHZNy4j4Kfj1
2wkCIQDzvrLI6n2g8C4n0UK6YJXbPs+MQwiGjvfTD/fxEZ8DPwIhAN5HShD5yKzG
SBzBPkBhhKKdmQQJefyXxxU8YZ+mi/FlAiBQKOoBO2Tfdb2oKKDKTJNcDDcTiMQY
BJKqHflLlaD23wIgGBBRtWrFTecdcXd+PMwR5uT25tf7y9vGosbprrYCGn0CIQCp
hKsIIRLCKesvnW7AAJPehP/J4maTdrKm5rcw8H5NqA==
-----END RSA PRIVATE KEY-----';

//setup the config file
$config = new \AuthSDK\Config();
$config->setBaseUrl('https://kaasbaas.localtunnel.me');
$config->setOrigin('digitalefactuur');
$config->setAuthServerTokenPrivateKey($privatekey);
$config->setClient(new \GuzzleHttp\Client());

//give guzzle client and config file to tokenpai when creating it
$tokenService = new \AuthSDK\TokenService(
    $config
);

$value = $tokenService->createAuthServerToken($privatekey);
var_dump($value);

//can be used to verify the results
$publicKey = '-----BEGIN PUBLIC KEY-----
MFwwDQYJKoZIhvcNAQEBBQADSwAwSAJBANOjP3Mafsgg5lR8oIQiueTiHE+gb/NE
HjI4roXKgrQi9QT7/dIoLZMdQGlBs4t1GBaUVUfactvTXspURUL/ltsCAwEAAQ==
-----END PUBLIC KEY-----';


