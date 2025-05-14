<?php

/*
 * @package OpenEMR
 *
 * @author Sherwin Gaddis <sherwingaddis@gmail.com>
 * Copyright (c) 2023.
 * @license "All rights reserved"
 */

require_once "../vendor/autoload.php";

use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Core\JWK;
    use Jose\Component\KeyManagement\JWKFactory;
    use Jose\Component\Signature\Algorithm\RS256;
use Jose\Component\Signature\JWSBuilder;
use Jose\Component\Signature\Serializer\CompactSerializer;

// The algorithm manager with the HS256 algorithm.
$algorithmManager = new AlgorithmManager(
    [
    new RS256(),
]
);

    $location = dirname(__FILE__, 6) . "/sites/serenity/documents/logs_and_misc";
    $jwk = JWKFactory::createFromKeyFile(
        $location . "/jaasauth.key",
        '',
    );

    $externalSourceKey = JWKFactory::createFromValues([
        'kid' => 'vpaas-magic-cookie-02bc0019d5a3438186239dc1711e0ee1/17d8ed',
        'kty' => 'RSA',
        'alg' => 'RS256',
        'use' => 'sig',
        'n' => $jwk,
        'e' => 'AQAB'
    ]);


// Our key.
$jwk = new JWK([
    'kty' => 'RSA',
    'k' => $externalSourceKey,
]);

// We instantiate our JWS Builder.
$jwsBuilder = new JWSBuilder($algorithmManager);


// The payload we want to sign. The payload MUST be a string hence we use our JSON Converter.
    $payload = json_encode([
        'iat' => time(),
        'nbf' => time(),
        'exp' => time() + 3600,
        'iss' => 'My service',
        'aud' => 'Your application',
    ]);

    $jws = $jwsBuilder
        ->create()                               // We want to create a new JWS
        ->withPayload($payload)                  // We set the payload
        ->addSignature($jwk, ['alg' => 'RS256']) // We add a signature with a simple protected header
        ->build();                               // We build it



    $serializer = new CompactSerializer(); // The serializer

    $token = $serializer->serialize($jws, 0); // We serialize the signature at index 0 (we only have one signature).

    echo $token;
