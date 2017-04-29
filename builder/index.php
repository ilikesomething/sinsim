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
    <script src="js/jquery.min.js"></script>
    <script src="js/jquery-ui.min.js"></script>
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
        <button class="icon icon-topbarlanguage object-right"><span></span></button>
    </div>
</header>
<div class="container padding-top-60">
    <div id="b_canvas">
        <svg id="b_container_lines"></svg>
        <div id="b_item_menu">
            <ul class="nav-menu">
                <li class="icon icon-viewpen">
                    <a data-type="1" class="b_item_menu_a">Edit text</a>
                </li>
                <li class="separator" class="b_item_menu_a"></li>
                <li class="icon icon-topbarclosed">
                    <a data-type="2" class="b_item_menu_a">Remove item</a>
                </li>
                <li class="icon icon-strategy-on">
                    <a data-type="3" class="b_item_menu_a">Remove link</a>
                </li>
                <li class="separator" class="b_item_menu_a"></li>
                <li>
                    <a data-type="0" class="b_item_menu_a">Cancel</a>
                </li>
            </ul>
        </div>
    </div>
</div>


<div class="modal in" tabindex="-1" role="dialog" aria-labelledby="modal" style="display: none">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Modal title</h3>
            </div>
            <div class="modal-body">
                Modal content...
            </div>
            <div class="modal-footer">
                <a class="button object-right" data-text="ACCEPT" data-dismiss="modal"><span>ACCEPT</span></a>
                <a class="button is-inverted object-right" data-text="CANCEL" data-dismiss="modal"><span>CANCEL</span></a>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    var items = <?= $items; ?>;
    var types = {"1": "Section", "2": "Question", "3": "Option"};

    const ITEM_TYPE_SECTION = 1;
    const ITEM_TYPE_QUESTION = 2;
    const ITEM_TYPE_OPTION = 3;

    const MENU_CANCEL = 0;
    const MENU_EDIT_TEXT = 1;
    const MENU_REMOVE_ITEM = 2;
    const MENU_REMOVE_LINK = 3;

    var currentDraggedLine = null;
    var isMenuOpened = false;
    var currentMenuLinkedObject = null;

    jQuery(document).ready(function ($) {


        //Init items & links on canvas
        var countItems = Object.keys(items).length;
        $.each(items, function (itemId, itemData) {

            //add each item
            var append = '<div class="b_item_draggable itemTypeBorder' + itemData.type + '"" id="' + itemId + '">';
            append += '<div class="b_item_draggable_header itemTypeBackground' + itemData.type + '">' + types[itemData.type] + '</div>';
            append += '<div class="b_item_draggable_content">' + itemData.name + '</div>';
            append += '<div class="b_button_newLine itemTypeBackground' + itemData.type + '"></div>';
            append += '<div class="b_item_menu_icon"><a href="#"><span class="icon icon-topbarmenu icon-24"></span></a></div>';
            append += '</div>';

            $('#b_canvas').append(append);
            $('#' + itemId).css({position: 'absolute', top: itemData.yPos, left: itemData.xPos});

            countItems--;
            if (countItems <= 0) {

                //when all items are added, we add the links between them
                $.each(items, function (startItemId, itemData) {

                    $.each(itemData.startLinks, function (k, endItemId) {

                        if (itemData.type != ITEM_TYPE_QUESTION) {
                            $('#' + startItemId).find('.b_button_newLine').hide();
                        }

                        createLink($('#' + startItemId), $('#' + endItemId));
                    });
                });
            }
        });


        //Menu open
        $('.b_item_menu_icon').on('click', function () {
            currentMenuLinkedObject = $(this).parent();
            showMenu($(this).parent());
        });


        $('.b_item_menu_a').on('click', function (e) {

            var thisObject = $(this);
            var thisObjectType = $(this).attr('data-type');


            isMenuOpened = false;
            $('#b_item_menu').fadeOut();

            if (currentMenuLinkedObject === null) {
                console.log('error');
                return;
            }


            var startObjectId = currentMenuLinkedObject.attr('id');

            /*
             const MENU_CANCEL = 0;
             const MENU_EDIT_TEXT = 1;
             const MENU_REMOVE_ITEM = 1;
             const MENU_REMOVE_LINK = 2;*/

            if (thisObjectType == MENU_REMOVE_LINK) {

                $.each(items[startObjectId].startLinks, function (key, data) {

                    removeLink(startObjectId, data);
                });

                $.each(items[startObjectId].endLinks, function (key, data) {

                    removeLink(data, startObjectId);
                });

                $('#' + startObjectId).find('.b_button_newLine').show();
            }

            // $('#modalExample').modal('show');

        });

        //Close menu if clicking elsewhere
        $(document).on('mousedown', function (e) {

            if (isMenuOpened) {

                var target = e.target;

                if (!$(target).hasClass('b_item_menu_a') && !$(target).parents().hasClass('b_item_menu_a') && !$(target).hasClass('b_item_menu_icon') && !$(target).parents().hasClass('b_item_menu_icon')) {

                    isMenuOpened = false;
                    $('#b_item_menu').hide();
                }
            }
        });

        function showMenu(itemObject) {

            isMenuOpened = true;

            //todo handle TYPE to display special items in the menu
            //this will change th width of the menu if we add or remove items

            var xPos = itemObject.position().left + itemObject.outerWidth();
            var yPos = itemObject.position().top;

            $('#b_item_menu').css({'top': yPos, 'left': xPos});
            $('#b_item_menu').fadeIn();
        }


        //Link mouse interactions
        $('#b_container_lines').on('click', 'line', function () {

            alert('TODO : window to select "Delete this link". For now we are gonna delete it automatically when closing this alert');

            var startObjectId = parseInt($(this).attr('data-startItemId'));
            var endObjectId = parseInt($(this).attr('data-endItemId'));

            $('#' + startObjectId).find('.b_button_newLine').show();
            removeLink(startObjectId, endObjectId);
        });


        $('#b_container_lines').on('mouseover', 'line', function () {

            if (currentDraggedLine !== null) {
                return;
            }

            $(this).css({'cursor': 'pointer', 'stroke': '#818181'});
        });

        $('#b_container_lines').on('mouseout', 'line', function () {

            if (currentDraggedLine !== null) {
                return;
            }

            $(this).css({'cursor': '', 'stroke': ''});
        });

        $(document).on('mousedown', '.b_item_draggable_header', function () {

            $(this).css({'cursor': 'move'});
        });

        $(document).on('mouseup', '.b_item_draggable_header', function () {

            $(this).css({'cursor': ''});
        });


        // When dragging an item
        $('.b_item_draggable').draggable({

            containment: "#b_canvas",

            drag: function (event, ui) {

                //Updating all connected links (visually)
                var objectId = parseInt($(event.target).attr('id'));

                $.each(items[objectId].startLinks, function (key, data) {

                    createLink($(event.target), $('#' + data));
                });

                $.each(items[objectId].endLinks, function (key, data) {

                    createLink($('#' + data), $(event.target));
                });
            },

            stop: function (event, ui) {

                var object = $(event.target);
                var objectId = parseInt(object.attr('id'));

                items[objectId].xPos = object.position().left;
                items[objectId].yPos = object.position().top;
            },
        });


        // When dragging the new line button
        $('.b_button_newLine').draggable({

            containment: "#b_canvas",

            drag: function (event, ui) {

                $(this).css({'cursor': 'move'});

                //Updating the link (visually)
                createLink($(this).parent(), $(this), true, true);
            },


            stop: function (event, ui) {

                // Reset button position & cursor
                $(this).css({top: '', left: '', 'cursor': ''});

                // Remove temporary link
                currentDraggedLine.remove();
                currentDraggedLine = null;
            }
        });


        // When dropping the new line button onto an item
        $('.b_item_draggable').droppable({

            //hoverClass: "b_item_draggableActive",
            accept: '.b_button_newLine',

            over: function (event, ui) {

                if (!isLinkAllowed($(ui.draggable).parent(), $(this))) {
                    $(this).addClass('b_item_draggableHoverNotAllowed');
                }
                else {
                    $(this).addClass('b_item_draggableHoverAllowed');
                }
            },

            out: function (event, ui) {

                $(this).removeClass("b_item_draggableHoverAllowed");
                $(this).removeClass("b_item_draggableHoverNotAllowed");
            },

            drop: function (event, ui) {

                $(this).removeClass("b_item_draggableHoverAllowed");
                $(this).removeClass("b_item_draggableHoverNotAllowed");

                var startObject = $(ui.draggable).parent();
                var startObjectId = parseInt(startObject.attr('id'));

                var endObject = $(this);
                var endObjectId = parseInt($(this).attr('id'));


                //Type 2 has multiple links allowed, others not
                if (items[startObjectId].type != ITEM_TYPE_QUESTION) {
                    removeLink(startObjectId, endObjectId);
                }

                if (!isLinkAllowed(startObject, endObject)) {
                    $('#' + startObjectId).find('.b_button_newLine').show();
                }
                else {

                    items[startObjectId].startLinks.push(endObjectId);
                    items[endObjectId].endLinks.push(startObjectId);

                    if (items[startObjectId].type != ITEM_TYPE_QUESTION) {
                        $(ui.draggable).hide();
                    }

                    createLink(startObject, endObject);
                }
            }
        });


        function createLink(startObject, endObject, temporary = false, customStartPoint = false) {

            var startObjectId = parseInt(startObject.attr('id'));
            var endObjectId = parseInt(endObject.attr('id'));

            if (!temporary || (temporary && currentDraggedLine === null)) {

                //if the link already exist and we wanted to create it, we are gonna update it instead
                if (!temporary && $('#link_' + startObjectId + '_' + endObjectId).length) {
                    currentLine = $('#link_' + startObjectId + '_' + endObjectId);
                }
                else {
                    //creating a new link
                    var currentLine = $(document.createElementNS('http://www.w3.org/2000/svg', 'line'));
                    $('#b_container_lines').append(currentLine);
                    // $(currentLine).css('stroke', $(startObjectId).css('border-color'));
                    currentLine.addClass('itemTypeStroke' + items[startObjectId].type);
                }

                //if we are dragging the new link button, we want to be able to update it every frame
                if (temporary) {
                    currentDraggedLine = currentLine;
                }
            }

            //at this point, if we are in temporary mode we should have "currentDraggedLine" filled
            if (temporary) {
                currentLine = currentDraggedLine;
            }
            else {
                //adding and ID to be able to access it later
                currentLine.attr('id', 'link_' + startObjectId + '_' + endObjectId);
                currentLine.attr('data-startItemId', startObjectId);
                currentLine.attr('data-endItemId', endObjectId);
            }


            // Calculating start positions

            var offset = startObject.position();

            var centerX = offset.left + (startObject.width() / 2) + 16; //last one is the padding not took into account
            // if (customStartPoint) {
            //starting from new line button place
            //var centerY = offset.top + startObject.height() - 4 + 16; //last one is the padding not took into account
            // }
            // else {
            //starting from middle of the block
            var centerY = offset.top + (startObject.height() / 2) + 16; //last one is the padding not took into account
            // }

            // Set start position
            currentLine.attr('x1', centerX);
            currentLine.attr('y1', centerY);


            // Calculating end positions
            if (temporary) {
                offset = endObject.position();

                //May cause performances issues. Since it's a child we need to calculate the position of the parent + the position of the child. Todo : looks for a workaround
                centerX = offset.left + endObject.parent().position().left + (endObject.width() / 2) + 2;
                centerY = offset.top + endObject.parent().position().top + (endObject.height() / 2) + 2;
            }
            else {
                offset = endObject.position();

                centerX = offset.left + (endObject.width() / 2) + 16; //last one is the padding not took into account
                centerY = offset.top + (endObject.height() / 2) + 16; //last one is the padding not took into account
            }


            // Set end position
            currentLine.attr('x2', centerX);
            currentLine.attr('y2', centerY);
        }


        function removeLink(startObjectId, endObjectId) {

            //we remove the link
            $('#link_' + startObjectId + '_' + endObjectId).remove();


            //we remove the start data of the link
            var startLink = items[startObjectId].startLinks.indexOf(parseInt(endObjectId));
            if (startLink > -1) {
                items[startObjectId].startLinks.splice(startLink, 1);
            }
            //we remove the end data of the link
            var endLink = items[endObjectId].endLinks.indexOf(parseInt(startObjectId));
            if (endLink > -1) {
                items[endObjectId].endLinks.splice(endLink, 1);
            }
        }

        function isLinkAllowed(startObject, endObject) {

            var startObjectId = parseInt(startObject.attr('id'));
            var endObjectId = parseInt(endObject.attr('id'));

            var startType = items[startObjectId].type;
            var endType = items[endObjectId].type;

            //a section can only link to a question or.. TODO
            if (startType == ITEM_TYPE_SECTION && (endType != ITEM_TYPE_QUESTION)) {
                return false;
            }
            //a question can only link to answers
            else if (startType == ITEM_TYPE_QUESTION && (endType != ITEM_TYPE_OPTION)) {
                return false;
            }
            //an answer can only link to a Section or a Question
            else if (startType == ITEM_TYPE_OPTION && (endType != ITEM_TYPE_SECTION && endType != ITEM_TYPE_QUESTION)) {
                return false;
            }
            //we don't allow to link an object to itself
            else if (startObjectId == endObjectId) {
                return false;// $('#' + startObjectId).find('.b_button_newLine').show();
            }
            //we also don't allow to link 2 objects in the 2 ways
            else if (items[endObjectId].startLinks.indexOf(parseInt(startObjectId)) > -1) {
                return false; // $('#' + startObjectId).find('.b_button_newLine').show();
            }
            else {
                return true;
            }

        }
    });
</script>
</body>
</html>