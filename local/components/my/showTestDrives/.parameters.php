<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$arComponentParameters = [
    'GROUPS' => [
        'TEST_DRIVES' => [
            'NAME' => 'Параметры бронирования',
        ],
    ],
    'PARAMETERS' => [
        'MODEL' => [
            'NAME' => 'Модель авто',
            'TYPE' => 'STRING',
            'PARENT' => 'USER_CARD',
        ],
        'DATE_START' => [
            'NAME' => 'Начало бронирования',
            'TYPE' => 'DATETIME',
            'PARENT' => 'USER_CARD',
        ],
        'DATE_END' => [
            'NAME' => 'Окончание бронирования',
            'TYPE' => 'DATETIME',
            'PARENT' => 'USER_CARD',
        ],
    ],
];
