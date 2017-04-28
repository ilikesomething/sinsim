<?php
//REMEMBER : don' use php since everything should work offline on the player side)

$items = '{
            "1":{
                "name":"This is a section (id 1)",
                "type":1,
                "xPos":200,
                "yPos":50,
                "startLinks":[2],
                "endLinks":[]
            },
            "2":{
                "name":"This is a question (id 2)",
                "type":2,
                "xPos":100,
                "yPos":150,
                "startLinks":[3],
                "endLinks":[1]
            },
            "3":{
                "name":"This is an answer (id 3)",
                "type":3,
                "xPos":200,
                "yPos":300,
                 "startLinks":[],
                "endLinks":[2]
            },
            "4":{
                "name":"This is an answer (id 4)",
                "type":3,
                "xPos":400,
                "yPos":400,
                 "startLinks":[],
                "endLinks":[]
            }
        }';

?>