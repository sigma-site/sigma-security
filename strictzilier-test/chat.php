<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST, OPTIONS');


/*
 * Răspundem la verificarea CORS.
 */

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}


/*
 * Citim cheia Vapi din variabila de mediu.
 */

$apiKey = getenv('VAPI_PRIVATE_KEY');

if (!$apiKey) {

    http_response_code(500);

    echo json_encode([
        'error' => 'VAPI_PRIVATE_KEY nu este configurată.'
    ]);

    exit;
}


/*
 * Citim datele trimise de pagina web.
 */

$input = json_decode(
    file_get_contents('php://input'),
    true
);


if (!is_array($input)) {

    http_response_code(400);

    echo json_encode([
        'error' => 'Date JSON invalide.'
    ]);

    exit;
}


$message = trim(
    $input['message'] ?? ''
);


$previousChatId =
    trim(
        $input['previousChatId'] ?? ''
    );


if ($message === '') {

    http_response_code(400);

    echo json_encode([
        'error' => 'Mesajul este gol.'
    ]);

    exit;
}


/*
 * ID-ul agentului STRICTZILIER.
 */

$assistantId =
    '970da2f1-89c8-438c-ac8c-30abfa04528e';


/*
 * Construim cererea către Vapi.
 */

$data = [

    'assistantId' => $assistantId,

    'input' => $message,

    'stream' => false

];


if ($previousChatId !== '') {

    $data['previousChatId'] =
        $previousChatId;

}


/*
 * Apel Vapi.
 */

$ch = curl_init(
    'https://api.vapi.ai/chat'
);


curl_setopt_array(
    $ch,
    [

        CURLOPT_POST => true,

        CURLOPT_RETURNTRANSFER => true,

        CURLOPT_HTTPHEADER => [

            'Authorization: Bearer ' . $apiKey,

            'Content-Type: application/json'

        ],

        CURLOPT_POSTFIELDS =>
            json_encode($data),

        CURLOPT_TIMEOUT => 60

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

    echo json_encode([
        'error' =>
            'Nu s-a putut contacta Vapi.',
        'details' =>
            $curlError
    ]);

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


if (
    $httpCode < 200 ||
    $httpCode >= 300
) {

    http_response_code(
        $httpCode ?: 502
    );

    echo json_encode([

        'error' =>
            'Vapi a returnat o eroare.',

        'vapi' =>
            $vapiResponse

    ]);

    exit;
}


/*
 * Extragem răspunsul agentului.
 */

$reply = '';


if (
    isset(
        $vapiResponse['output'][0]['content']
    )
) {

    $reply =
        $vapiResponse['output'][0]['content'];

}


if ($reply === '') {

    http_response_code(502);

    echo json_encode([

        'error' =>
            'Vapi nu a returnat un răspuns text.',

        'vapi' =>
            $vapiResponse

    ]);

    exit;
}


/*
 * Trimitem către pagina web doar ce avem nevoie.
 */

echo json_encode(

    [

        'ok' => true,

        'reply' => $reply,

        'chatId' =>
            $vapiResponse['id'] ?? null

    ],

    JSON_UNESCAPED_UNICODE

);
