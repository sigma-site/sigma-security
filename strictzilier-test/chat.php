<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST, OPTIONS');


/*
 * Răspundem la cererea CORS.
 */

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}


/*
 * =========================================================
 * CITIREA CHEII VAPI DIN .env
 * =========================================================
 *
 * PieHost a creat:
 *
 * /app/.env
 *
 * iar chat.php se află în:
 *
 * /app/strictzilier-test/chat.php
 *
 * De aceea urcăm un nivel și citim:
 *
 * /app/.env
 */

$apiKey = '';

$envFile = dirname(__DIR__) . '/.env';


if (file_exists($envFile)) {

    $lines = file(
        $envFile,
        FILE_IGNORE_NEW_LINES |
        FILE_SKIP_EMPTY_LINES
    );

    if ($lines !== false) {

        foreach ($lines as $line) {

            $line = trim($line);


            /*
             * Ignorăm liniile goale și comentariile.
             */

            if (
                $line === '' ||
                str_starts_with($line, '#')
            ) {
                continue;
            }


            /*
             * Căutăm exact variabila Vapi.
             */

            if (
                strpos(
                    $line,
                    'VAPI_PRIVATE_KEY='
                ) === 0
            ) {

                $apiKey =
                    substr(
                        $line,
                        strlen('VAPI_PRIVATE_KEY=')
                    );


                /*
                 * Eliminăm eventualele ghilimele.
                 */

                $apiKey =
                    trim(
                        $apiKey,
                        " \t\n\r\0\x0B\"'"
                    );


                break;
            }
        }
    }
}


/*
 * Dacă nu găsim cheia, oprim aici.
 */

if ($apiKey === '') {

    http_response_code(500);

    echo json_encode(
        [
            'error' =>
                'VAPI_PRIVATE_KEY nu a fost găsită în .env.'
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


/*
 * =========================================================
 * CITIM MESAJUL PRIMIT DE LA PAGINA WEB
 * =========================================================
 */

$rawInput =
    file_get_contents(
        'php://input'
    );


$input =
    json_decode(
        $rawInput,
        true
    );


if (!is_array($input)) {

    http_response_code(400);

    echo json_encode(
        [
            'error' =>
                'Date JSON invalide.'
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


$message =
    trim(
        $input['message'] ?? ''
    );


$previousChatId =
    trim(
        $input['previousChatId'] ?? ''
    );


if ($message === '') {

    http_response_code(400);

    echo json_encode(
        [
            'error' =>
                'Mesajul este gol.'
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


/*
 * =========================================================
 * AGENTUL STRICTZILIER
 * =========================================================
 */

$assistantId =
    '970da2f1-89c8-438c-ac8c-30abfa04528e';


/*
 * =========================================================
 * PREGĂTIM CEREREA CĂTRE VAPI
 * =========================================================
 */

$data = [

    'assistantId' =>
        $assistantId,

    'input' =>
        $message,

    'stream' =>
        false

];


/*
 * Dacă există o conversație anterioară,
 * păstrăm contextul acesteia.
 */

if ($previousChatId !== '') {

    $data['previousChatId'] =
        $previousChatId;
}


/*
 * =========================================================
 * APEL API VAPI
 * =========================================================
 */

$ch =
    curl_init(
        'https://api.vapi.ai/chat'
    );


curl_setopt_array(
    $ch,
    [

        CURLOPT_POST =>
            true,

        CURLOPT_RETURNTRANSFER =>
            true,

        CURLOPT_HTTPHEADER =>
            [

                'Authorization: Bearer ' .
                    $apiKey,

                'Content-Type: application/json'

            ],

        CURLOPT_POSTFIELDS =>
            json_encode(
                $data
            ),

        CURLOPT_TIMEOUT =>
            60

    ]
);


$response =
    curl_exec($ch);


$httpCode =
    curl_getinfo(
        $ch,
        CURLINFO_HTTP_CODE
    );


$curlError =
    curl_error($ch);


curl_close($ch);


/*
 * =========================================================
 * EROARE DE CONEXIUNE
 * =========================================================
 */

if ($response === false) {

    http_response_code(502);

    echo json_encode(
        [
            'error' =>
                'Nu s-a putut contacta Vapi.',

            'details' =>
                $curlError
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


/*
 * =========================================================
 * DECODĂM RĂSPUNSUL VAPI
 * =========================================================
 */

$vapiResponse =
    json_decode(
        $response,
        true
    );


/*
 * =========================================================
 * VAPI A RETURNAT O EROARE
 * =========================================================
 */

if (
    $httpCode < 200 ||
    $httpCode >= 300
) {

    http_response_code(
        $httpCode ?: 502
    );

    echo json_encode(
        [

            'error' =>
                'Vapi a returnat o eroare.',

            'vapi' =>
                $vapiResponse

        ],
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


/*
 * =========================================================
 * PENTRU PRIMUL TEST
 * RETURNĂM RĂSPUNSUL VAPI COMPLET
 * =========================================================
 *
 * Nu extragem încă reply-ul.
 *
 * Vrem mai întâi să vedem structura reală
 * returnată de endpoint-ul /chat.
 */

echo json_encode(
    [

        'ok' =>
            true,

        'vapi' =>
            $vapiResponse

    ],
    JSON_UNESCAPED_UNICODE
);
