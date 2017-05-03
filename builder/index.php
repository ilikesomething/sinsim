<?php require_once('head.php'); ?><!DOCTYPE html>
<html lang="en">
<head>
    <title>S</title>
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
            <h2>SinSim Builder</h2>
        </div>
        <button class="icon icon-floppy object-right"><span></span></button>
        <button class="icon icon-action-share-on object-right"><span></span></button>
    </div>
</header>
<div class="container padding-top-60">
    <div id="b_canvas">
        <svg id="b_container_lines"></svg>
        <div id="b_item_menu">
            <ul class="nav-menu">
                <div id="b_canvas_menu_1">
                    <li class="icon icon-viewpen">
                        <a data-type="1" class="b_menu_a">Edit text</a>
                    </li>
                    <li class="icon icon-postdocument-off">
                        <a data-type="2" class="b_menu_a">Attach a resource (TODO)</a>
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
                        class="icon icon-carat object-right color-grey3"></span></a>
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
            <a data-type="5" class="b_menu_a disabled">Mail event (TODO)</a>
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

    var app = <?= $mainData; ?>;
    var items = <?= $items; ?>;

</script>
<script src="js/builder.js"></script>
</body>
</html>