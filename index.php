<!DOCTYPE html>
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
    <link rel="stylesheet" type="text/css" href="css/style2.css">
    <script src="js/jquery.min.js"></script>
    <script src="js/jquery-ui.min.js"></script>
</head>
<body>
<noscript>
    <div id="noscript-warning">You need JavaScript to be enabled in order to work properly</div>
</noscript>

<div id="b_canvas">

    <svg id="b_container_lines"></svg>

    <div class="b_item_draggable" data-id="1">1
        <div class="b_button_newLine"></div>
    </div>

    <div class="b_item_draggable" data-id="2">2
        <div class="b_button_newLine"></div>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function ($) {

        // When dragging an item
        $('.b_item_draggable').draggable({

            containment: "#b_canvas",

            drag: function (event, ui) {

                //console.log($(this).attr('data-id'));

                //Todo : update start/end offset of connected lines
            },
        });


        /*
         var startItemPosition = $(event.target).parent().position();
         console.log('start ' + startItemPosition);

         var endTargetedPosition = $(event.target).position();
         console.log('end ' + endTargetedPosition);
         */
        // $(this).closest('.boxConnectPoint');




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


                //Todo if dropped in the droppable, (not here) : create permanant line from the middle of the parent element to the middle of next element
            }
        });


        // When dropping the new line button onto an item
        $('.b_item_draggable').droppable({

            accept: '.b_button_newLine',

            drop: function (event, ui) {

                console.log('dropped on '+$(this).attr('data-id'));
                console.log('button dropped from '+$(ui.draggable).parent().attr('data-id'));

                drawLink($(ui.draggable).parent(), $(this));

                //todo keep track of the lines dropped on each block to be able to update them when dragging block
            }
        });




        var currentDraggedLine = null;

        function drawLink(startObject, endObject, temporary = false, customStartPoint = false) {


            if (!temporary || (temporary && currentDraggedLine === null)) {

                var currentLine = $(document.createElementNS('http://www.w3.org/2000/svg', 'line'));
                $('#b_container_lines').append(currentLine);

                if(temporary) {
                    currentDraggedLine = currentLine;
                }
            }

            if(temporary) {
                currentLine = currentDraggedLine;
            }


            // Calculating start positions
            var offset = startObject.offset();
            var centerX = offset.left + startObject.width() / 2;

            if(customStartPoint) {
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