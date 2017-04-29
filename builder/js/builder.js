const ITEM_TYPE_SECTION = 1;
const ITEM_TYPE_QUESTION = 2;
const ITEM_TYPE_OPTION = 3;

const MENU_ITEM_CANCEL = 0;
const MENU_ITEM_EDIT_TEXT = 1;
const MENU_ITEM_REMOVE = 2;
const MENU_ITEM_REMOVE_LINK = 3;


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

                $.each(itemData.startLinks, function (k, endItemId) {

                    if (itemData.type != ITEM_TYPE_QUESTION) {
                        $('#' + startItemId).find('.b_button_newLine').hide();
                    }

                    createLink($('#' + startItemId), $('#' + endItemId));
                });
            });
        }
    });

    function createItem(itemData, itemId) {

        //add each item
        var append = '<div class="b_item_draggable itemTypeBorder' + itemData.type + '" id="' + itemId + '">';
        append += '<div class="b_item_draggable_header itemTypeBackground' + itemData.type + '">' + types[itemData.type] + '</div>';
        append += '<div class="b_item_draggable_content">' + itemData.name[app.mainLanguage] + '</div>';
        append += '<div class="b_button_newLine itemTypeBackground' + itemData.type + '"></div>';
        append += '<div class="b_item_menu_icon"><a href="#"><span class="icon icon-topbarmenu icon-24"></span></a></div>';
        append += '</div>';
        $('#b_canvas').append(append);
        $('#' + itemId).css({position: 'absolute', top: itemData.yPos, left: itemData.xPos});

    }


    /******************************* CANVAS MENU *******************************/

        //Open canvas menu
    $('#b_canvas').on('click', function (e) {
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

        var mainLanguage = app.mainLanguage;

        var itemId = Object.keys(items).length + 1;

        var position = $('#b_canvas_menu').position();

        var itemData = {
            "name": {},
            "type": thisObjectType,
            "xPos": position.left,
            "yPos": position.top,
            "startLinks": [],
            "endLinks": []
        };

        itemData.name[mainLanguage] = ""; // ¯\_(ツ)_/¯

        items[itemId] = itemData;
        createItem(itemData, itemId);

        draggableRefreshItem(); // ¯\_(ツ)_/¯
        draggableRefreshButtonNewLine(); // ¯\_(ツ)_/¯
        droppableRefreshItem(); // ¯\_(ツ)_/¯
    });


    function showCanvasMenu(e) {

        isCanvasMenuOpened = true;

        var xPos = e.clientX;
        var yPos = e.clientY;

        $('#b_canvas_menu').css({'top': yPos, 'left': xPos});
        $('#b_canvas_menu').fadeIn();
    }


    /******************************* ITEM MENU *******************************/

        //Open menu of an item
    $(document).on('click', '.b_item_menu_icon', function () {
        currentMenuLinkedObject = $(this).parent();
        showItemMenu($(this).parent());
    });


    //When clicking on an link of the item's menu
    $('#b_item_menu .b_menu_a').on('click', function (e) {

        var thisObject = $(this);
        var thisObjectType = $(this).attr('data-type');

        isItemMenuOpened = false;
        $('#b_item_menu').fadeOut();

        var startObjectId = currentMenuLinkedObject.attr('id');

        if (thisObjectType == MENU_ITEM_EDIT_TEXT) {

            $.each(items[startObjectId].name, function (key, data) {
                $('#modalItemEditTextInput_' + key).val(data);
            });

            $('#modalItemEditText').modal('show');
        }
        else if (thisObjectType == MENU_ITEM_REMOVE) {

            removeAllItemLinks(startObjectId);

            //todo check if it does not have a chance to fire before "removeAllItemLinks" .each functions. it would break everything
            //delete items[startObjectId];
            $('#' + startObjectId).remove();
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
            var objectId = currentMenuLinkedObject.attr('id');
            var defaultLangText = $('#modalItemEditTextInput_' + app.mainLanguage).val();

            if (defaultLangText.length > 140) {
                defaultLangText = defaultLangText.slice(0, 140) + '...';
            }

            $(object).find('.b_item_draggable_content').text(defaultLangText);

            items[objectId].name = [];
            $.each(app.languages, function (key, data) {

                items[objectId].name[data] = $('#modalItemEditTextInput_' + data).val();

            });
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
    }

    //Link mouse interactions
    $('#b_container_lines').on('click', 'line', function () {

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
        }
        //we remove the end data of the link
        var endLink = items[endObjectId].endLinks.indexOf(parseInt(startObjectId));
        if (endLink > -1) {
            items[endObjectId].endLinks.splice(endLink, 1);
        }
    }


    function removeAllItemLinks(startObjectId) {

        var startLinks = $.merge([], items[startObjectId].startLinks); //to prevent link with the original array
        var endLinks = $.merge([], items[startObjectId].endLinks);

        $.each(startLinks, function (key, data) {
            removeLink(startObjectId, data);
        });

        $('#' + startObjectId).find('.b_button_newLine').show();

        $.each(endLinks, function (key, data) {
            $('#' + data).find('.b_button_newLine').show();
            removeLink(data, startObjectId);
        });
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


    function showItemMenu(itemObject) {

        isItemMenuOpened = true;

        //todo handle TYPE to display special items in the menu
        //this will change th width of the menu if we add or remove items

        var xPos = itemObject.position().left + itemObject.outerWidth();
        var yPos = itemObject.position().top;

        $('#b_item_menu').css({'top': yPos, 'left': xPos});
        $('#b_item_menu').fadeIn();
    }
});