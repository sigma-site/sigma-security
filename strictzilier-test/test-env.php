<?php

header('Content-Type: application/json; charset=utf-8');

$results = [];

$results['getenv'] =
    getenv('VAPI_PRIVATE_KEY') !== false;

$results['env'] =
    isset($_ENV['VAPI_PRIVATE_KEY']);

$results['server'] =
    isset($_SERVER['VAPI_PRIVATE_KEY']);


/*
 * Verificăm dacă .env există în rădăcina aplicației.
 */

$rootEnv =
    dirname(__DIR__) . '/.env';

$results['root_env_exists'] =
    file_exists($rootEnv);


/*
 * Verificăm și dacă există .env în directorul
 * strictzilier-test.
 */

$localEnv =
    __DIR__ . '/.env';

$results['local_env_exists'] =
    file_exists($localEnv);


/*
 * Dacă fișierul există, verificăm doar dacă
 * apare numele VAPI_PRIVATE_KEY în el.
 *
 * NU afișăm valoarea cheii.
 */

if (file_exists($rootEnv)) {

    $content =
        file_get_contents($rootEnv);

    $results['root_contains_vapi_key'] =
        strpos(
            $content,
            'VAPI_PRIVATE_KEY='
        ) !== false;
}


if (file_exists($localEnv)) {

    $content =
        file_get_contents($localEnv);

    $results['local_contains_vapi_key'] =
        strpos(
            $content,
            'VAPI_PRIVATE_KEY='
        ) !== false;
}


echo json_encode(
    $results,
    JSON_PRETTY_PRINT |
    JSON_UNESCAPED_UNICODE
);
