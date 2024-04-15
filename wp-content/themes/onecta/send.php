<?php

try {
    $env = parse_ini_file('.env');

    $ch = curl_init();

    $headers = [
        'Accept: application/json',
        'Content-Type: application/json'
    ];

    $get = [
        'fields' => [
            'TITLE' => 'Заявка с сайта',
            'NAME' => preg_replace('/[^\p{L}\p{N}\s]/u', '', $_GET['name']),
            'STATUS_ID' => 'NEW',
            'OPENED' => 'Y',
            'ASSIGNED_BY_ID' => 'Y',
            'PHONE' => [
                [
                    'VALUE' => preg_replace('/[^0-9]/', '', $_GET['phone']),
                    'VALUE_TYPE' => 'WORK'
                ]
            ],
            'EMAIL' => [
                [
                    'VALUE' => filter_var($_GET['email'], FILTER_VALIDATE_EMAIL) ? $_GET['email'] : '',
                    'VALUE_TYPE' => 'WORK'
                ]
            ]
        ]
    ];

    curl_setopt($ch, CURLOPT_URL, $env['BITRIX24_ENDPOINT_LEAD_ADD'] . '?' . http_build_query($get));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Timeout in seconds
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $return = curl_exec($ch);
    $result = !empty($return) ? json_decode($return) : null;
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($httpcode !== 200) {
        require_once(__DIR__ . '/views/error.html');
    }

    require_once(__DIR__ . '/views/success.html');
    require_once(__DIR__ . '/views/main.html');
} catch (\Throwable $th) {
    require_once(__DIR__ . '/views/error.html');
}