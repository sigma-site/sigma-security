<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST, OPTIONS');


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}


/*
 * Citim cheia Vapi.
 *
 * Mai întâi încercăm getenv().
 * Dacă PieHost nu expune variabila prin getenv(),
 * citim direct fișierul .env din rădăcina proiectului.
 */

$apiKey = getenv('VAPI_PRIVATE_KEY');


if (!$apiKey) {

    $envFile = dirname(__DIR__) . '/.env';

    if (file_exists($envFile)) {

        $lines = file(
            $envFile,
            FILE_IGNORE_NEW_LINES |
            FILE_SKIP_EMPTY_LINES
        );

        foreach ($lines as $line) {

            $line = trim($line);

            if (
                $line === '' ||
                str_starts_with($line, '#')
            ) {
                continue;
            }

            if (
                strpos($line, 'VAPI_PRIVATE_KEY=') === 0
            ) {

                $apiKey =
                    substr(
                        $line,
                        strlen('VAPI_PRIVATE_KEY=')
                    );

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


if (!$apiKey) {

    http_response_code(500);

    echo json_encode(
        [
            'error' =>
                'VAPI_PRIVATE_KEY nu este configurată.'
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


/*
 * Citim mesajul primit de la pagina web.
 */

$input = json_decode(
    file_get_contents('php://input'),
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
 * Agentul STRICTZILIER.
 */

$assistantId =
    '970da2f1-89c8-438c-ac8c-30abfa04528e';


/*
 * Pregătim cererea către Vapi.
 */

$data = [

    'assistantId' =>
        $assistantId,

    'input' =>
        $message,

    'stream' =>
        false

];


if ($previousChatId !== '') {

    $data['previousChatId'] =
        $previousChatId;

}


/*
 * Apel către Vapi.
 */

$ch = curl_init(
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
 * Eroare de conexiune.
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
 * Decodăm răspunsul Vapi.
 */

$vapiResponse =
    json_decode(
        $response,
        true
    );


/*
 * Vapi a returnat o eroare.
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
 * Pentru moment returnăm răspunsul Vapi
 * aproape integral, ca să vedem exact
 * structura răspunsului real.
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
