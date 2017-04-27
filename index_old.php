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
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <script src="js/jquery.min.js"></script>
    <script src="js/jquery-ui.min.js"></script>
</head>
<body>
<noscript>
    <div id="noscript-warning">You need JavaScript to be enabled in order to work properly</div>
</noscript>

<div id="canvas">
    <svg id="linesContainer"></svg>

    <div class="draggableItem item-1" data-id="1">1
        <div class="newLineButton"></div>
        <div class="boxConnectPoint"></div>
    </div>
    <div class="draggableItem item-2" data-id="2">2
        <div class="newLineButton"></div>
        <div class="boxConnectPoint"></div>
    </div>
    <div class="draggableItem item-3" data-id="3">3
        <div class="newLineButton"></div>
        <div class="boxConnectPoint"></div>
    </div>
</div>


<script type="text/javascript">

    jQuery(document).ready(function ($) {

        var linesContainer = $('#linesContainer');

        /*** When dragging an item ***/
        $('.draggableItem').draggable({

            containment: "#canvas",

            drag: function (event, ui) {


                var boxConnectPoint = $(this).closest('.boxConnectPoint');

                if(boxConnectPoint) {
                    console.log(boxConnectPoint.offsetLeft);
                }



                var lines = $(this).data('lines');
                if (lines) {

                    lines.forEach(function (line, id) {
                        $(line).attr('x1', $(this).position().left).attr('y1', $(this).position().top);
                    }.bind(this));
                }

                var con_lines = $(this).data('connected-lines');
                if (con_lines) {

                    con_lines.forEach(function (con_line, id) {
                        $(con_line).attr('x2', $(this).position().left).attr('y2', $(this).position().top);
                    }.bind(this));
                }
            }
        });


        /*** When dropping the button (and line) of an item onto another item ***/
        $('.draggableItem').droppable({

            accept: '.newLineButton',

            drop: function (event, ui) {

                var startItem = ui.draggable.closest('.draggableItem');

                //startItem == startItem
                //this == end item
                //this connected-item = startItem

                $(this).data('connected-item', startItem);


                //reset button position
                ui.draggable.css({top: '', left: ''});

                startItem.data('lines').push(startItem.data('line'));

                if ($(this).data('connected-lines')) {

                    $(this).data('connected-lines').push(startItem.data('line'));

                    var y2_ = parseInt(startItem.data('line').attr('y2'));
                    startItem.data('line').attr('y2', y2_ + $(this).data('connected-lines').length * 5);
                }
                else {
                    $(this).data('connected-lines', [startItem.data('line')]);
                }

                startItem.data('line', null);
            }
        });


        /*** When dragging the button (and line) of an item ***/
        $('.newLineButton').draggable({

            containment: "#canvas",

            /*** When dragging the button (and line) ***/
            drag: function (event, ui) {


                var startPosition = $(event.target).parent().position();
                var endPosition = $(event.target).position();

                if (startPosition && endPosition) {

                    $(event.target).parent().data('line').attr('x2', endPosition.left + startPosition.left).attr('y2', endPosition.top + startPosition.top);
                }
            },

            /*** When releasing the button (and line) dragged ***/
            stop: function (event, ui) {


                if (!ui.helper.closest('.draggableItem').data('line')) {
                    return;
                }

                ui.helper.css({top: '', left: ''});
                ui.helper.closest('.draggableItem').data('line').remove();
                ui.helper.closest('.draggableItem').data('line', null);
            }
        });


        /*** When clicking the drag button of an item ***/
        $('.newLineButton').on('mousedown', function (e) {

            var newLine;
            var startItem = $(this).closest('.draggableItem');


            if (!$(startItem).data('lines')) {

                $(startItem).data('lines', []);
            }


            if (!$(startItem).data('line')) {

                newLine = $(document.createElementNS('http://www.w3.org/2000/svg', 'line'));
                startItem.data('line', newLine);
            }
            else {
                newLine = startItem.data('line');
            }


            linesContainer.append(newLine);


            var start = startItem.position();

            /*newLine.attr('x1',start.left).attr('y1',start.top+1);
             newLine.attr('x2',start.left+1).attr('y2',start.top+1);*/

            newLine.attr('x1', start.left).attr('y1', start.top);
            newLine.attr('x2', start.left).attr('y2', start.top);
        });
    });
</script>
</body>
</html>
