<?php

$lang_data = [

    40 => [
        'name' => 'English',
        'code' => 'en-GB'
    ],
    47 => [
        'name' => 'French',
        'code' => 'fr-FR'
    ],
    133 => [
        'name' => 'Russian',
        'code' => 'ru-RU'
    ],
];

$lang_default = 40;
$lang_available = [40,47,133];

$mainData = '
        {
            "languages":["en-GB", "fr-FR", "ru-RU"],
            "mainLanguage":"en-GB"
        }
';

$items = '{
            "1":{
                "name":{
                    "en-GB":"This is a section (id 1)",
                    "fr-FR":"C\'est une section (id 1)"
                },
                "type":1,
                "xPos":200,
                "yPos":50,
                "startLinks":[2],
                "endLinks":[]
            },
            "2":{
                 "name":{
                    "en-GB":"This is a question (id 2)",
                },
                "type":2,
                "xPos":100,
                "yPos":150,
                "startLinks":[3],
                "endLinks":[1]
            },
            "3":{
                 "name":{
                    "en-GB":"This is an answer (id 3)",
                },
                "type":3,
                "xPos":200,
                "yPos":300,
                 "startLinks":[],
                "endLinks":[2]
            },
            "4":{
                 "name":{
                    "en-GB":"This is an answer (id 4)",
                },
                "type":3,
                "xPos":400,
                "yPos":400,
                 "startLinks":[],
                "endLinks":[]
            }
        }';

?>