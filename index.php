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

<div id="b_canvas">
    <svg id="b_container_lines"></svg>
</div>

<script type="text/javascript">

    var items = <?= $items; ?>;

    jQuery(document).ready(function ($) {


        //Init items & links on canvas
        var countItems = Object.keys(items).length;
        $.each(items, function (itemId, itemData) {

            var append = '<div class="b_item_draggable" id="' + itemId + '">' + itemData.name;
            append += '<div class="b_button_newLine"></div>';
            append += '</div>';

            $('#b_canvas').append(append);
            $('#' + itemId).css({position: 'absolute', top: itemData.yPos, left: itemData.xPos});

            countItems--;
            if (countItems <= 0) {

                $.each(items, function (startItemId, itemData) {

                    $.each(itemData.startLinks, function (k, endItemId) {

                        drawLink($('#' + startItemId), $('#' + endItemId));
                    });
                });
            }
        });


        // When dragging an item
        $('.b_item_draggable').draggable({

            containment: "#b_canvas",

            drag: function (event, ui) {

                var objectId = parseInt($(event.target).attr('id'));

                $.each(items[objectId].startLinks, function (key, data) {

                    drawLink($(event.target), $('#' + data));
                });

                $.each(items[objectId].endLinks, function (key, data) {

                    drawLink($('#' + data), $(event.target));
                });
            },
        });


        // When dragging the new line button
        $('.b_button_newLine').draggable({

            containment: "#b_canvas",

            drag: function (event, ui) {

                drawLink($(this).parent(), $(this), true, true);
            },


            stop: function (event, ui) {

                // Reset button position
                $(this).css({top: '', left: ''});

                // Remove temporary line
                currentDraggedLine.remove();
                currentDraggedLine = null;
            }
        });


        // When dropping the new line button onto an item
        $('.b_item_draggable').droppable({

            accept: '.b_button_newLine',

            drop: function (event, ui) {



                var startObject = $(ui.draggable).parent();
                var startObjectId = parseInt(startObject.attr('id'));

                var endObject = $(this);
                var endObjectId = parseInt($(this).attr('id'));


                var startLinks = items[startObjectId].startLinks;
                var countItems = startLinks.length;
                if (countItems > 0) {

                    $.each(startLinks, function (key, data) {

                        $('#link_' + startObjectId + '_' + data).remove();

                        var endLink = items[data].endLinks.indexOf(parseInt(startObjectId));
                        if(endLink > -1) {
                            items[data].endLinks.splice(endLink, 1);
                        }

                        countItems--;
                        if (countItems <= 0) {

                            if(startObjectId == endObjectId) {
                                items[startObjectId].startLinks = [];
                            }
                            else {

                                items[startObjectId].startLinks = [endObjectId];
                                items[endObjectId].endLinks.push(startObjectId);
                                drawLink(startObject, endObject);
                            }
                        }
                    });
                }
                else {

                    items[startObjectId].startLinks = [endObjectId];
                    items[endObjectId].endLinks.push(startObjectId);

                    drawLink(startObject, endObject);
                }
            }
        });


        var currentDraggedLine = null;

        function drawLink(startObject, endObject, temporary = false, customStartPoint = false) {

            var startObjectId = parseInt(startObject.attr('id'));
            var endObjectId = parseInt(endObject.attr('id'));

            if (!temporary || (temporary && currentDraggedLine === null)) {

                if ($('#link_' + startObjectId + '_' + endObjectId).length) {
                    currentLine = $('#link_' + startObjectId + '_' + endObjectId);
                }
                else {

                    var currentLine = $(document.createElementNS('http://www.w3.org/2000/svg', 'line'));
                    $('#b_container_lines').append(currentLine);
                }

                if (temporary) {
                    currentDraggedLine = currentLine;
                }
            }

            if (temporary) {
                currentLine = currentDraggedLine;
            }
            else {
                currentLine.attr('id', 'link_' + startObjectId + '_' + endObjectId);
            }


            // Calculating start positions
            var offset = startObject.offset();
            var centerX = offset.left + startObject.width() / 2;

            if (customStartPoint) {
                //starting from new line button place
                var centerY = offset.top + startObject.height() - 4;
            }
            else {
                //starting from middle of the block
                var centerY = offset.top + startObject.height() / 2;
            }

            // Set start position
            currentLine.attr('x1', centerX);
            currentLine.attr('y1', centerY);


            // Calculating end positions
            offset = endObject.offset();
            centerX = offset.left + endObject.width() / 2;
            centerY = offset.top + endObject.height() / 2;

            // Set end position
            currentLine.attr('x2', centerX);
            currentLine.attr('y2', centerY);
        }

    });
</script>
</body>
</html>