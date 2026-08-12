<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST, OPTIONS');


/*
|--------------------------------------------------------------------------
| CORS
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}


/*
|--------------------------------------------------------------------------
| CITIRE VAPI PRIVATE KEY
|--------------------------------------------------------------------------
|
| PieHost pune .env în:
|
| /app/.env
|
| iar acest fișier este:
|
| /app/strictzilier-test/chat.php
|
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

            if (
                $line === '' ||
                str_starts_with($line, '#')
            ) {
                continue;
            }

            if (
                strpos(
                    $line,
                    'VAPI_PRIVATE_KEY='
                ) === 0
            ) {

                $apiKey = substr(
                    $line,
                    strlen('VAPI_PRIVATE_KEY=')
                );

                $apiKey = trim(
                    $apiKey,
                    " \t\n\r\0\x0B\"'"
                );

                break;
            }
        }
    }
}


/*
|--------------------------------------------------------------------------
| VERIFICARE CHEIE
|--------------------------------------------------------------------------
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
|--------------------------------------------------------------------------
| CITIRE DATE PRIMITE
|--------------------------------------------------------------------------
*/

$rawInput = file_get_contents(
    'php://input'
);


$input = json_decode(
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


$message = trim(
    $input['message'] ?? ''
);


$previousChatId = trim(
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
|--------------------------------------------------------------------------
| ASSISTANT STRICTZILIER
|--------------------------------------------------------------------------
*/

$assistantId =
    '970da2f1-89c8-438c-ac8c-30abfa04528e';


/*
|--------------------------------------------------------------------------
| PREGĂTIRE CERERE VAPI
|--------------------------------------------------------------------------
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
|--------------------------------------------------------------------------
| APEL VAPI
|--------------------------------------------------------------------------
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
                $data,
                JSON_UNESCAPED_UNICODE
            ),

        CURLOPT_TIMEOUT =>
            60

    ]
);


$response = curl_exec($ch);


$httpCode = curl_getinfo(
    $ch,
    CURLINFO_HTTP_CODE
);


$curlError = curl_error($ch);


curl_close($ch);


/*
|--------------------------------------------------------------------------
| EROARE CONEXIUNE
|--------------------------------------------------------------------------
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
|--------------------------------------------------------------------------
| DECODARE RĂSPUNS VAPI
|--------------------------------------------------------------------------
*/

$vapiResponse = json_decode(
    $response,
    true
);


if (!is_array($vapiResponse)) {

    http_response_code(502);

    echo json_encode(
        [
            'error' =>
                'Răspuns Vapi invalid.',

            'raw' =>
                $response
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| EROARE VAPI
|--------------------------------------------------------------------------
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
|--------------------------------------------------------------------------
| EXTRAGEM RĂSPUNSUL AGENTULUI
|--------------------------------------------------------------------------
|
| Conform API-ului Vapi:
|
| output[0].role
| output[0].content
|
*/

$reply = '';


if (
    isset($vapiResponse['output']) &&
    is_array($vapiResponse['output'])
) {

    foreach (
        $vapiResponse['output']
        as $outputMessage
    ) {

        if (
            isset($outputMessage['role']) &&
            $outputMessage['role'] === 'assistant' &&
            isset($outputMessage['content'])
        ) {

            $reply =
                $outputMessage['content'];

            break;
        }
    }
}


/*
|--------------------------------------------------------------------------
| DACĂ NU GĂSIM RĂSPUNSUL
|--------------------------------------------------------------------------
*/

if ($reply === '') {

    http_response_code(502);

    echo json_encode(
        [
            'error' =>
                'Agentul nu a returnat un răspuns.',

            'vapi' =>
                $vapiResponse
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| RĂSPUNS CĂTRE PAGINA WEB
|--------------------------------------------------------------------------
*/

echo json_encode(
    [

        'ok' =>
            true,

        'reply' =>
            $reply,

        'chatId' =>
            $vapiResponse['id'] ?? null

    ],
    JSON_UNESCAPED_UNICODE
);
