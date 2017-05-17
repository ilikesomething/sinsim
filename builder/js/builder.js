var types = {
    "1": "Round",
    "2": "Info",
    "3": "Question",
    "4": "Option (Single)",
    "5": "Option (Multiple)",
    "6": "Score variation"
};

const ITEM_TYPE_ROUND = 1;
const ITEM_TYPE_INFO = 2;
const ITEM_TYPE_QUESTION = 3;
const ITEM_TYPE_OPTION_RADIO = 4;
const ITEM_TYPE_OPTION_CHECKBOX = 5;
const ITEM_TYPE_SCORE_VARIATION = 6;

const MENU_ITEM_CANCEL = 0;
const MENU_ITEM_EDIT_TEXT = 1;
const MENU_ITEM_ADD_RESOURCE = 2;
const MENU_ITEM_EDIT_SCORE = 3;
const MENU_ITEM_REMOVE = 4;
const MENU_ITEM_REMOVE_LINK = 5;


const MENU_CANVAS_CANCEL = 0;
const MENU_CANVAS_SECTION = 1;
const MENU_CANVAS_QUESTION = 2;
const MENU_CANVAS_OPTION = 3;


var currentDraggedLine = null;
var isCanvasMenuOpened = false;

var isItemMenuOpened = false;
var currentMenuLinkedObject = null;

jQuery(document).ready(function ($) {

    /******************************* INIT *******************************/

    var countItems = Object.keys(items).length;
    $.each(items, function (itemId, itemData) {

        createItem(itemData, itemId);

        countItems--;
        if (countItems <= 0) {

            //when all items are added, we add the links between them
            $.each(items, function (startItemId, itemData) {

                if (itemData.startLinks.length == 0) {
                    $('#' + startItemId + '_item').find('.b_button_newLine').show();
                }

                if (itemData.type == ITEM_TYPE_QUESTION) {
                    $('#' + startItemId + '_item').find('.b_button_newLine').show();
                }

                $.each(itemData.startLinks, function (k, endItemId) {

                    createLink($('#' + startItemId + '_item'), $('#' + endItemId + '_item'));
                });
            });
        }
    });


    $.each(app.languages, function (key, data) {

        var append = '' +
            '<div class="form-group">' +
            '<label class="form-label" for="modalItemEditTextInput_' + key + '">' + data.name + '</label>' +
            '<textarea class="form-control" rows="1" placeholder="' + data.name + '" id="modalItemEditTextInput_' + key + '"></textarea>' +
            '</div>';

        $('#modalItemEditText form').append(append);
    });


    $.each(app.scores, function (key, data) {

        var append = '<option value="' + key + '">' + data.name + '</option>';
        $('#modalItemEditScoreSelected').append(append);
    });


    function createItem(itemData, itemId) {

        //add each item
        var append;
        append = '<div class="b_item_draggable itemTypeBorder' + itemData.type + '" id="' + itemId + '_item">';
        append += '<div class="b_item_draggable_header itemTypeBackground' + itemData.type + '">' + types[itemData.type] + '</div>';

        if (itemData.type == ITEM_TYPE_SCORE_VARIATION) {

            var addScoreClass = 'positiveScore';
            if (itemData.value < 0) {
                addScoreClass = 'negativeScore';
            }

            append += '<div class="b_item_draggable_content ' + addScoreClass + '">' + app.scores[itemData.target].name + '<br/><span>' + itemData.value + '</span></div>';
        }
        else {
            append += '<div class="b_item_draggable_content">' + itemData.name[app.mainLanguage] + '</div>';
        }

        append += '<div class="b_button_newLine itemTypeBackground' + itemData.type + '"></div>';
        append += '<div class="b_item_menu_icon"><a href="#"><span class="icon icon-topbarmenu icon-24"></span></a></div>';
        append += '</div>';
        $('#b_canvas').append(append);
        $('#' + itemId + '_item').css({position: 'absolute', top: itemData.yPos, left: itemData.xPos});

    }


    /******************************* CANVAS MENU *******************************/

    //Open canvas menu
    $('#b_canvas').contextmenu(function (e) {

        e.preventDefault();

        var target = e.target;
        if ($(target).attr('id') == 'b_canvas' || $(target).attr('id') == 'b_container_lines') {
            showCanvasMenu(e);
        }
    });

    //When clicking on an link of the canvas menu
    $('#b_canvas_menu .b_menu_a').on('click', function (e) {

        var thisObject = $(this);
        var thisObjectType = $(this).attr('data-type');

        isCanvasMenuOpened = false;
        $('#b_canvas_menu').fadeOut();

        if (thisObjectType == 0) {
            return;
        }

        var mainLanguage = app.mainLanguage;

        var itemId = Object.keys(items).length + 1;

        var positionMenu = $('#b_canvas_menu').position();
        var positionCanvas = $('#b_canvas').position();
        //var positionCanvasParent = $('#b_canvas').parent().position();

        var parentWidth = 0;
        if (parseInt($('#b_canvas').parent().css("width")) > parseInt($('#b_canvas').css("width"))) {
            parentWidth = (parseInt($('#b_canvas').parent().css("width")) - parseInt($('#b_canvas').css("width"))) / 2;
        }

        var itemData = {
            "name": {},
            "type": thisObjectType,
            "xPos": positionMenu.left - positionCanvas.left - parentWidth,
            "yPos": positionMenu.top - positionCanvas.top,
            "startLinks": [],
            "endLinks": [],
            "value": "+0",
            "target": "",
        };


        $.each(app.scores, function (key, data) {

            //taking the first score as default
            itemData.target = key + "";
            return false;
        });


        itemData.name[mainLanguage] = ""; // ¯\_(ツ)_/¯

        items[itemId] = itemData;
        updateJsonDisplay();
        createItem(itemData, itemId);

        $('#' + itemId + '_item').find('.b_button_newLine').show();

        draggableRefreshItem(); // ¯\_(ツ)_/¯
        draggableRefreshButtonNewLine(); // ¯\_(ツ)_/¯
        droppableRefreshItem(); // ¯\_(ツ)_/¯
    });


    function showCanvasMenu(e) {

        isCanvasMenuOpened = true;

        var xPos = e.clientX;
        var yPos = e.clientY;

        $('.menuToActivate').hide();

        $('#b_canvas_menu').css({'top': yPos, 'left': xPos});
        $('#b_canvas_menu').fadeIn();
    }


    /******************************* ITEM MENU *******************************/

    //Open menu of an item
    $(document).on('click', '.b_item_menu_icon', function (e) {
        e.preventDefault();
        currentMenuLinkedObject = $(this).parent();
        showItemMenu($(this).parent());
    });


    //When clicking on an link of the item's menu
    $('#b_item_menu .b_menu_a').on('click', function (e) {

        var thisObject = $(this);
        var thisObjectType = $(this).attr('data-type');

        isItemMenuOpened = false;
        $('#b_item_menu').fadeOut();

        var startObjectId = parseInt(currentMenuLinkedObject.attr('id'));

        if (thisObjectType == MENU_ITEM_EDIT_TEXT) {

            $('#modalItemEditText').find('.modal-header h3 span').text(types[items[startObjectId].type]);

            $.each(items[startObjectId].name, function (key, data) {
                $('#modalItemEditTextInput_' + key).val(data);
            });

            $('#modalItemEditText').modal('show');
        }
        else if (thisObjectType == MENU_ITEM_EDIT_SCORE) {

            $('#modalItemEditScoreInput').val(items[startObjectId].value);

            var target = parseInt(items[startObjectId].target);

            $('#modalItemEditScoreSelected option:eq(' + target + ')').prop('selected', true);
            $('#modalItemEditScoreSelected option[value="' + target + '"]').prop('selected', 'selected').change();

            $('#modalItemEditScore').modal('show');
        }
        else if (thisObjectType == MENU_ITEM_REMOVE) {

            removeAllItemLinks(startObjectId);

            //todo check if it does not have a chance to fire before "removeAllItemLinks" .each functions. it would break everything
            delete items[startObjectId];
            updateJsonDisplay();
            $('#' + startObjectId + '_item').remove();
        }
        else if (thisObjectType == MENU_ITEM_REMOVE_LINK) {

            removeAllItemLinks(startObjectId);
        }
    });


    //Closing the menu if clicking elsewhere
    $(document).on('mousedown', function (e) {

        if (isItemMenuOpened) {

            var target = e.target;

            if (!$(target).hasClass('b_menu_a') && !$(target).parents().hasClass('b_menu_a') && !$(target).hasClass('b_item_menu_icon') && !$(target).parents().hasClass('b_item_menu_icon')) {

                isItemMenuOpened = false;
                $('#b_item_menu').hide();
            }
        }
        else if (isCanvasMenuOpened) {

            var target = e.target;

            if (!$(target).hasClass('b_menu_a') && !$(target).parents().hasClass('b_menu_a')) {

                isCanvasMenuOpened = false;
                $('#b_canvas_menu').hide();
            }
        }
    });


    /******************************* MODALS *******************************/

    //When submitting a modal
    $('.submitModal').on('click', function (e) {

        //Modal : Edit text of an item
        if ($(this).attr('data-id') == 'modalItemEditText') { //rather than searching in parent or whatever, in case of changes in the html


            var object = currentMenuLinkedObject;
            var objectId = parseInt(currentMenuLinkedObject.attr('id'));
            var defaultLangText = $('#modalItemEditTextInput_' + app.mainLanguage).val();

            if (defaultLangText.length > 140) {
                defaultLangText = defaultLangText.slice(0, 140) + '...';
            }

            $(object).find('.b_item_draggable_content').text(defaultLangText);


            $.each(app.languages, function (key, data) {

                items[objectId].name[key] = $('#modalItemEditTextInput_' + key).val();
            });
            updateJsonDisplay();
        }
        else if ($(this).attr('data-id') == 'modalItemEditScore') { //rather than searching in parent or whatever, in case of changes in the html

            var object = currentMenuLinkedObject;
            var objectId = parseInt(currentMenuLinkedObject.attr('id'));

            var scoreInput = $('#modalItemEditScoreInput').val();

            if (scoreInput.indexOf('+') === -1 && scoreInput.indexOf('-') === -1 && scoreInput != "0") {
                scoreInput = '+' + scoreInput;
            }


            $(object).find('.b_item_draggable_content').removeClass('positiveScore');
            $(object).find('.b_item_draggable_content').removeClass('negativeScore');

            var addScoreClass = 'positiveScore';
            if (scoreInput < 0) {
                addScoreClass = 'negativeScore';
            }

            $(object).find('.b_item_draggable_content').addClass(addScoreClass);


            $(object).find('.b_item_draggable_content').html(app.scores[$("#modalItemEditScoreSelected").val()].name + '<br/><span>' + scoreInput + '</span>');

            items[objectId].value = scoreInput;
            items[objectId].target = $("#modalItemEditScoreSelected").val();
            updateJsonDisplay();
        }
    });


    /******************************* ITEM LINKS *******************************/

    draggableRefreshButtonNewLine();
    function draggableRefreshButtonNewLine() {

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
    }

    droppableRefreshItem();
    function droppableRefreshItem() {
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
                    $('#' + startObjectId + '_item').find('.b_button_newLine').show();
                }
                else {

                    items[startObjectId].startLinks.push(endObjectId);
                    items[endObjectId].endLinks.push(startObjectId);

                    updateJsonDisplay();

                    createLink(startObject, endObject);

                    if (items[startObjectId].startLinks.length > 0 && items[startObjectId].type != ITEM_TYPE_QUESTION) {
                        $('#' + startObjectId + '_item').find('.b_button_newLine').hide();
                    }

                    if (items[endObjectId].startLinks.length == 0 || items[endObjectId].type == ITEM_TYPE_QUESTION) {
                        $('#' + endObjectId + '_item').find('.b_button_newLine').show();
                    }
                }
            }
        });
    }

    //Link mouse interactions
    $('#b_container_lines').on('click', 'line', function () {

        var startObjectId = parseInt($(this).attr('data-startItemId'));
        var endObjectId = parseInt($(this).attr('data-endItemId'));

        $('#' + startObjectId + '_item').find('.b_button_newLine').show();
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


    /******************************* ITEM *******************************/

    draggableRefreshItem();
    function draggableRefreshItem() {

        // When dragging an item
        $('.b_item_draggable').draggable({

            containment: "#b_canvas",

            drag: function (event, ui) {

                //Updating all connected links (visually)
                var objectId = parseInt($(event.target).attr('id'));

                $.each(items[objectId].startLinks, function (key, data) {

                    createLink($(event.target), $('#' + data + '_item'));
                });

                $.each(items[objectId].endLinks, function (key, data) {

                    createLink($('#' + data + '_item'), $(event.target));
                });
            },

            stop: function (event, ui) {

                var object = $(event.target);
                var objectId = parseInt(object.attr('id'));

                items[objectId].xPos = object.position().left;
                items[objectId].yPos = object.position().top;
                updateJsonDisplay();
            },
        });
    }


    /******************************* FUNCTIONS *******************************/

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
            updateJsonDisplay();
        }
        //we remove the end data of the link
        var endLink = items[endObjectId].endLinks.indexOf(parseInt(startObjectId));
        if (endLink > -1) {
            items[endObjectId].endLinks.splice(endLink, 1);
            updateJsonDisplay();
        }

        if (items[startObjectId].startLinks.length == 0) {
            $('#' + startObjectId + '_item').find('.b_button_newLine').show();
        }

        if (items[startObjectId].type == ITEM_TYPE_QUESTION) {
            $('#' + startObjectId + '_item').find('.b_button_newLine').show();
        }
    }


    function removeAllItemLinks(startObjectId) {

        var startLinks = $.merge([], items[startObjectId].startLinks); //to prevent link with the original array
        var endLinks = $.merge([], items[startObjectId].endLinks);

        $.each(startLinks, function (key, data) {
            removeLink(startObjectId, data);
        });

        $('#' + startObjectId + '_item').find('.b_button_newLine').show();

        $.each(endLinks, function (key, data) {
            $('#' + data + '_item').find('.b_button_newLine').show();
            removeLink(data, startObjectId);
        });
    }


    function isLinkAllowed(startObject, endObject) {

        var startObjectId = parseInt(startObject.attr('id'));
        var endObjectId = parseInt(endObject.attr('id'));

        var startType = items[startObjectId].type;
        var endType = items[endObjectId].type;

        //a section can only link to a question or..
        if (startType == ITEM_TYPE_INFO && (endType != ITEM_TYPE_INFO && endType != ITEM_TYPE_QUESTION && endType != ITEM_TYPE_ROUND)) {
            return false;
        }
        else if (startType == ITEM_TYPE_SCORE_VARIATION && (endType != ITEM_TYPE_INFO && endType != ITEM_TYPE_QUESTION && endType != ITEM_TYPE_ROUND)) {
            return false;
        }
        //a question can only link to answers
        else if (startType == ITEM_TYPE_QUESTION && (endType != ITEM_TYPE_OPTION_RADIO && endType != ITEM_TYPE_OPTION_CHECKBOX)) {
            return false;
        }
        //an answer can only link to a Section or a Question
        else if ((startType == ITEM_TYPE_OPTION_RADIO || startType == ITEM_TYPE_OPTION_CHECKBOX) && (endType != ITEM_TYPE_INFO && endType != ITEM_TYPE_QUESTION && endType != ITEM_TYPE_ROUND && endType != ITEM_TYPE_SCORE_VARIATION )) {
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


    function showItemMenu(itemObject) {

        isItemMenuOpened = true;

        $('#b_canvas_menu_1').hide();
        $('#b_canvas_menu_2').hide();

        if (items[parseInt(itemObject.attr('id'))].type == ITEM_TYPE_SCORE_VARIATION) {
            $('#b_canvas_menu_2').show();
        }
        else {
            $('#b_canvas_menu_1').show();
        }


        var xPos = itemObject.position().left + itemObject.outerWidth();
        var yPos = itemObject.position().top;

        $('#b_item_menu').css({'top': yPos, 'left': xPos});
        $('#b_item_menu').fadeIn();
    }

    function updateJsonDisplay(){

        $('#jsonDisplay').text(JSON.stringify(items));
    }

    updateJsonDisplay();
});