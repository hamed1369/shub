function repositionCover() {
    $('.cover-wrapper').hide();
    $('.cover-resize-wrapper').show();
    $('.cover-resize-buttons').show();
    $('.drag-div').show();
    $('.default-buttons').hide();
    $('.screen-width').val($('.cover-resize-wrapper').width());
    $('.cover-resize-wrapper img')
        .css('cursor', 's-resize')
        .draggable({
            scroll: false,

            axis: "y",

            cursor: "s-resize",

            drag: function (event, ui) {
                y1 = $('.timeline-header-wrapper').height();
                y2 = $('.cover-resize-wrapper').find('img').height();

                if (ui.position.top >= 0) {
                    ui.position.top = 0;
                }
                else if(ui.helper[0].clientHeight<=(y1 - y2)){
                    ui.position.top = 0;
                }
                else if (ui.position.top <= (y1 - y2)) {
                    ui.position.top = y1 - y2;
                }
            },

            stop: function (event, ui) {
                $('input.cover-position').val(ui.position.top);
                $('input.cover-photo-height').val($('.cover-container').height());
            }
        });
}

function saveReposition() {

    if ($('input.cover-position').length == 1) {
        posY = $('input.cover-position').val();
        $('form.cover-position-form').submit();
    }
}

function cancelReposition() {
    $('.cover-wrapper').show();
    $('.cover-resize-wrapper').hide();
    $('.cover-resize-buttons').hide();
    $('.default-buttons').show();
    $('input.cover-position').val(0);
    $('.cover-resize-wrapper img').draggable('destroy').css('cursor', 'default');
}