<?php
/*
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
*/

$mainData = '
        {
            "languages":{
                "en-GB":{"name":"English"},
                "fr-FR":{"name":"French"}, 
                "ru-RU":{"name":"Russian"}
            },
            "mainLanguage":"en-GB",
            "scores":{
                "40":{"name":"Performance score (40)"},
                "48":{"name":"Global score (48)"}
            }
        }
';

$items = '{
            "0":{
                "name":{
                    "en-GB":"This is round (id 0)",
                    "fr-FR":"C\'est un round (id 0)"
                },
                "type":1,
                "xPos":400,
                "yPos":10,
                "startLinks":[1],
                "endLinks":[],
                "value":"",
                "target":""
            },
            "1":{
                "name":{
                    "en-GB":"This is a step info (id 1)",
                    "fr-FR":"C\'est une step info (id 1)"
                },
                "type":2,
                "xPos":200,
                "yPos":200,
                "startLinks":[2],
                "endLinks":[0],
                "value":"",
                 "target":""
            },
            "2":{
                 "name":{
                    "en-GB":"This is a question (id 2)",
                },
                "type":3,
                "xPos":250,
                "yPos":400,
                "startLinks":[3],
                "endLinks":[1],
                "value":"",
                 "target":""
            },
            "3":{
                 "name":{
                    "en-GB":"This is an answer (id 3)",
                },
                "type":4,
                "xPos":400,
                "yPos":600,
                 "startLinks":[5],
                "endLinks":[2],
                "value":"",
                 "target":""
            },
            "4":{
                 "name":{
                    "en-GB":"This is an answer (id 4)",
                },
                "type":5,
                "xPos":100,
                "yPos":600,
                 "startLinks":[],
                "endLinks":[],
                "value":"",
                 "target":""
            },
            "5":{
                 "name":{
                    "en-GB":"",
                },
                "type":6,
                "xPos":500,
                "yPos":700,
                 "startLinks":[],
                "endLinks":[3],
                
                "value":"-10",
                 "target":"40"
            },
            "6":{
                 "name":{
                    "en-GB":"",
                },
                "type":6,
                "xPos":10,
                "yPos":700,
                 "startLinks":[],
                "endLinks":[],
                
                "value":"+13",
                "target":"48"
            }
        }';

?>