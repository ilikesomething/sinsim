<!DOCTYPE html>
<html lang="en">
<head>
    <title>Sinsim Builder Prototype</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=0.7">
    <meta name="description" content=":D"/>
    <link rel="icon" type="image/png" href="favicon.png"/>
    <!--[if IE]>
    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico"/><![endif]-->
    <link rel="stylesheet" type="text/css" href="css/changr.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
<noscript>
    <div id="noscript-warning">You need JavaScript to be enabled in order to work properly</div>
</noscript>

<header class="topbar">
    <div class="topbar-container container">
        <button class="icon icon-topbarmenu object-left"><span></span></button>
        <div class="topbar-name">
            <h2>SinSim Builder Prototype</h2>
        </div>
        <button class="icon icon-floppy object-right"><span></span></button>
        <button class="icon icon-action-share-on object-right"><span></span></button>
    </div>
</header>
<div class="container">

    <p class="text-center">JSON live output</p>
    <div id="jsonDisplay"></div>

    <p class="text-center">Right click on the canvas to create a new item</p>
    <div id="b_canvas">
        <svg id="b_container_lines"></svg>
        <div id="b_item_menu">
            <ul class="nav-menu">
                <div id="b_canvas_menu_1">
                    <li class="icon icon-viewpen">
                        <a data-type="1" class="b_menu_a">Edit text</a>
                    </li>
                    <li class="icon icon-postdocument-off disabled">
                        <a data-type="2" class="b_menu_a">Attach a resource</a>
                    </li>
                </div>
                <div id="b_canvas_menu_2">
                    <li class="icon icon-viewpen">
                        <a data-type="3" class="b_menu_a">Edit score</a>
                    </li>
                </div>
                <li class="separator" class="b_menu_a"></li>
                <li class="icon icon-topbarclosed">
                    <a data-type="4" class="b_menu_a">Remove item</a>
                </li>
                <li class="icon icon-strategy-on">
                    <a data-type="5" class="b_menu_a">Remove links</a>
                </li>
                <li class="separator" class="b_menu_a"></li>
                <li>
                    <a data-type="0" class="b_menu_a">Cancel</a>
                </li>
            </ul>
        </div>
    </div>
</div>
<div id="b_canvas_menu">
    <ul class="nav-menu">
        <li class="icon icon-action-programcontent-on">
            <a data-type="1" class="b_menu_a">Round</a>
        </li>
        <li class="icon icon-action-programinfo-off">
            <a data-type="2" class="b_menu_a">Info</a>
        </li>
        <li class="icon icon-viewpen">
            <a data-type="3" class="b_menu_a">Question</a>
        </li>


        <li class="hoverable icon icon-viewcheck">
            <a data-type="4" class="b_menu_a" data-toggle="dropdown"><span>Option</span><span
                        class="icon object-right color-grey3"></span></a>
            <ul class="nav-menu is-nowrap">
                <li>
                    <a data-type="4" class="b_menu_a">Single choice</a>
                </li>
                <li>
                    <a data-type="5" class="b_menu_a">Multiple choice</a>
                </li>
            </ul>
        </li>
        <li class="icon icon-action-contact-off">
            <a data-type="5" class="b_menu_a disabled">Mail event</a>
        </li>
        <li class="icon icon-refresh">
            <a data-type="6" class="b_menu_a">Score variation</a>
        </li>
        <li class="separator" class="b_menu_a"></li>
        <li>
            <a data-type="0" class="b_menu_a">Cancel</a>
        </li>
    </ul>
</div>

<div class="modal fade" id="modalItemEditText" tabindex="-1" role="dialog" aria-labelledby="modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Edit <span></span> text</h3>
            </div>
            <div class="modal-body">
                <form>
                </form>
            </div>
            <div class="modal-footer">
                <a class="button object-right submitModal" data-id="modalItemEditText" data-text="SAVE"
                   data-dismiss="modal" data-toggle="infobar" data-target="#infobar" data-label="Changes applied"><span>SAVE</span></a>
                <a class="button is-inverted object-right" data-text="CANCEL"
                   data-dismiss="modal"><span>CANCEL</span></a>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modalItemEditScore" tabindex="-1" role="dialog" aria-labelledby="modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Edit score</h3>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label class="form-label" for="modalItemEditScoreSelected">Select a score</label>
                        <select class="form-control" id="modalItemEditScoreSelected">
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="modalItemEditScoreInput">Set a variation</label>
                        <input class="form-control" type="text" id="modalItemEditScoreInput" placeholder="+100">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <a class="button object-right submitModal" data-id="modalItemEditScore" data-text="SAVE"
                   data-dismiss="modal" data-toggle="infobar" data-target="#infobar" data-label="Changes applied"><span>SAVE</span></a>
                <a class="button is-inverted object-right" data-text="CANCEL"
                   data-dismiss="modal"><span>CANCEL</span></a>
            </div>
        </div>
    </div>
</div>

<script src="js/jquery.min.js"></script>
<script src="js/jquery-ui.min.js"></script>
<script src="http://dev.changr.com/framework/js/app/app.js"></script>

<script type="text/javascript">

    var app = {
        "languages": {
            "en-GB": {"name": "English"},
            "fr-FR": {"name": "French"},
            "ru-RU": {"name": "Russian"}
        },
        "mainLanguage": "en-GB",
        "scores": {
            "40": {"name": "Performance score"},
            "48": {"name": "Global score"}
        }
    };

    var items = {
        "1": {
            "name": {"en-GB": "Start of the round 1", "fr-FR": "Début du round 1", "ru-RU": ""},
            "type": "1",
            "xPos": 405,
            "yPos": 10,
            "startLinks": [2],
            "endLinks": [],
            "value": "+0",
            "target": "40"
        },
        "2": {
            "name": {
                "en-GB": "This is a step info, you will be able to attach an image, animation, video, link ect.. to any of the items (not just the step info)",
                "fr-FR": "",
                "ru-RU": ""
            }, "type": "2", "xPos": 339, "yPos": 133, "startLinks": [3], "endLinks": [1], "value": "+0", "target": "40"
        },
        "3": {
            "name": {"en-GB": "Do you like your Job ?", "fr-FR": "", "ru-RU": ""},
            "type": "3",
            "xPos": 342,
            "yPos": 307,
            "startLinks": [4, 5],
            "endLinks": [2],
            "value": "+0",
            "target": "40"
        },
        "4": {
            "name": {"en-GB": "YES YES VERY MUCH !", "fr-FR": "", "ru-RU": ""},
            "type": "4",
            "xPos": 78,
            "yPos": 400,
            "startLinks": [6],
            "endLinks": [3],
            "value": "+0",
            "target": "40"
        },
        "5": {
            "name": {"en-GB": "HELL NO !!!!!!", "fr-FR": "", "ru-RU": ""},
            "type": "4",
            "xPos": 616,
            "yPos": 379,
            "startLinks": [7],
            "endLinks": [3],
            "value": "+0",
            "target": "40"
        },
        "6": {
            "name": {"en-GB": ""},
            "type": "6",
            "xPos": 89,
            "yPos": 703,
            "startLinks": [12],
            "endLinks": [4, 8],
            "value": "+10",
            "target": "48"
        },
        "7": {
            "name": {"en-GB": "Why do you hate your Job ?", "fr-FR": "", "ru-RU": ""},
            "type": "3",
            "xPos": 619,
            "yPos": 514,
            "startLinks": [8, 9, 10],
            "endLinks": [5],
            "value": "+0",
            "target": "40"
        },
        "8": {
            "name": {"en-GB": "Nevermind, i love it", "fr-FR": "", "ru-RU": ""},
            "type": "4",
            "xPos": 364,
            "yPos": 665,
            "startLinks": [6],
            "endLinks": [7],
            "value": "+0",
            "target": "40"
        },
        "9": {
            "name": {"en-GB": "I hate working !!!", "fr-FR": "", "ru-RU": ""},
            "type": "4",
            "xPos": 591,
            "yPos": 668,
            "startLinks": [11],
            "endLinks": [7],
            "value": "+0",
            "target": "40"
        },
        "10": {
            "name": {"en-GB": "I hate everything !!!", "fr-FR": "", "ru-RU": ""},
            "type": "4",
            "xPos": 792,
            "yPos": 668,
            "startLinks": [11],
            "endLinks": [7],
            "value": "+0",
            "target": "40"
        },
        "11": {
            "name": {"en-GB": ""},
            "type": "6",
            "xPos": 685,
            "yPos": 813,
            "startLinks": [12],
            "endLinks": [9, 10],
            "value": "-10",
            "target": "48"
        },
        "12": {
            "name": {
                "en-GB": "Before you continue, you should know that you are very valuable to us",
                "fr-FR": "",
                "ru-RU": ""
            },
            "type": "2",
            "xPos": 267,
            "yPos": 893,
            "startLinks": [13],
            "endLinks": [6, 11],
            "value": "+0",
            "target": "40"
        },
        "13": {
            "name": {"en-GB": "Round 2...ect..", "fr-FR": "", "ru-RU": ""},
            "type": "1",
            "xPos": 424,
            "yPos": 1079,
            "startLinks": [],
            "endLinks": [12],
            "value": "+0",
            "target": "40"
        }
    };

</script>
<script src="js/builder.js"></script>
</body>
</html>