var tp = {
    state: null,
    dt: {},
    draw: {
        mousePressed: false,
        touchPressed: false,
        lastMouseX: null, 
        lastMouseY: null,
        lastTouchX: null, 
        lastTouchY: null,
        ctx: null,
    }
}


$(document).ready( function () {

    getInitState();
    
    // Initialize filter selects
    initFilterSelect();

    // Initialize user selects
    initUserSelect();

    // initialize canvas
    initCanvas();

    // initialize tooltip
    $('[data-toggle="tooltip"]').tooltip();

    refreshAll(['app', 'user', 'label', 'card', 'example']);

    init_quiz();


    $(document).on('click', '.tp-audio', function (event) {

        var src = $(this).data('path');
        var audio = new Audio(src);
        audio.load();
        audio.play();
        return;

    });

    $(document).on('click', '#tp_filter_clear', function (event) {
        clearFilters();
        refreshAll(['data']);
    });

    $(document).on('click', '.tp_rel', function (event) {

        var filter = $('#tp_filter_' + tp.state.app);
        tp.state.app = $(this).data('dest');

        clearFilters();
        var option = new Option($(this).data('text'), $(this).data('id'), true, true );
        filter.append(option).trigger('change', true);

        refreshAll(['app']);
    });

    $(document).on('change', '#tp_filter_label', function (event, noRefresh = false) {

        tp.state.filter.label = JSON.stringify($(this).select2('data'));
        if (!noRefresh){
            refreshAll(['data']);
        }

    });

    $(document).on('change', '#tp_filter_card', function (event, noRefresh = false) {
        tp.state.filter.card = JSON.stringify($(this).select2('data'));
        if (!noRefresh){
            refreshAll(['data']);
        }
    });

    $(document).on('change', '#tp_filter_example', function (event, noRefresh = false) {
        tp.state.filter.example = JSON.stringify($(this).select2('data'));
        if (!noRefresh){
            refreshAll(['data']);
        }
    });

    $(document).on('change', '#tp_filter_user', function (event) {
        tp.state.filter.user = JSON.stringify($(this).select2('data'));
        clearFilters();
        refreshAll(['data']);
    });

    $(document).on('click', '.tp_link', function (event) {
        tp.state.app = $(this).data('app');
        refreshAll(['app']);
    });

    $(document).on('click', '#tp-show-hide', function (event) {

        var state = (state_ids.tp_quiz_state == 'Show' ? 'Hide' : 'Show');

        $('#tp-show-hide').text(state);
        state_ids.tp_quiz_state = state;

        store_state();

        refresh_all();

    });

    $(document).on('click', '.tp-toggle', function (event) {

        var id = this.id;
        var index = state_ids.tp_quiz_shown.indexOf(id);

        if (index > -1){
            state_ids.tp_quiz_shown.splice(index, 1);
            $('.' + id).attr('data-state', 'off');
        } else {
            state_ids.tp_quiz_shown.push(id);
            $('.' + id).attr('data-state', 'on');
        }

        store_state();

        refresh_all();

    });

    $(document).on('click', '#tp-canvas-clear', function (event) {
        // Use the identity matrix while clearing the canvas
        ctx.setTransform(1, 0, 0, 1, 0, 0);
        ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
    });

    $(document).on('shown.bs.modal', '#tp_modal_card', function (event) {

        // get operation from button that triggered the modal form
        var button = $(event.relatedTarget);
        var op = button.data('op');

        switch(op) {
        case "new":

            // configure modal form
            $('#tp_modal_title').html('Create Card');
            $('#_method_change').val('POST');

            // initialize modal form values
            $('#tp_modal_card #tp_symbol').val('');
            $('#tp_modal_card #tp_pinyin').val('');
            $('#tp_modal_card #tp_translation').val('');
            $('#tp_modal_card #tp_comment').val('');
            $('#tp_modal_card #tp_labels').val([]).change();
            $('#tp_modal_card #tp_examples').val([]).change();

            break;

        case "edit":
        case "clone":

            var data = table.row(button.parents('tr')).data();
            console.log(data);

            // configure modal form
            if (op == "edit"){
                $('#tp_modal_title').html('Edit Card');
                $('#_method_change').val('PUT');
                $('#tp_modal_card_form').attr('action', '/cards/' + data.id);
            } else {
                $('#tp_modal_title').html('Clone Card');
                $('#_method_change').val('POST');
            }

            // code block
            $('#tp_modal_card #tp_symbol').val(data.symbol.symbol);
            $('#tp_modal_card #tp_pinyin').val(data.pinyin);
            $('#tp_modal_card #tp_translation').val(data.translation);
            $('#tp_modal_card #tp_comment').val(data.comment);
            $('#tp_modal_card #tp_examples').val(data.examples.ids).change();

            $('#tp_modal_card #tp_labels').val(null).trigger('change');
            $('#tp_modal_card #tp_labels').append('data', [{id: '10', text: 'text10'}, {id: '20', text: 'text20'}]);

            /* $('#tp_modal_card #tp_labels').val(JSON.parse(el_tr.find('[tp_item="tp_labels"]').attr('tp_value'))).change(); */
            /* $('#tp_modal_card #tp_examples').val(JSON.parse(el_tr.find('[tp_item="tp_examples"]').attr('tp_value'))).change(); */

            break;

        default:

            // code block
            console.assert(true);
        } 

    });

    $(document).on('shown.bs.modal', '#tp_modal_card_delete', function (event) {

        // get card id
        var button = $(event.relatedTarget);
        var el_tr = $(button).closest('tr');
        var id = el_tr.data('id');

        // update modal form content
        $('#tp_modal_card_delete_form').attr('action', '/cards/' + id);
        $('#tp_modal_card_delete_text').html('Do you really want to delete '  + id + '?');

    });
} );

function refreshDatatableLabel(){

    if ('label' in tp.dt){
        tp.dt.label.ajax.reload(function() {
            $('#tp_label').show();
        });
    } else {
        tp.dt.label = $('#tp_label_table').DataTable({
            order: [[ 0, "desc" ]],
            processing: true,
            serverSide: true,
            serverMethod: 'post',
            ajax: {
                url: '/datatable',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: {
                    tpApp : 'label',
                    tpUserData: function() { return tp.state.filter.user; },
                    tpLabelData: function() { return tp.state.filter.label; },
                    tpCardData: function() { return tp.state.filter.card; },
                    tpExampleData: function() { return tp.state.filter.example; },
                }
            },
            columnDefs: [{
                    className: "text-center",
                    targets: [2, 3],
                }, { 
                    searchable: false,
                    visible: false, 
                    targets: 0,
                }, {
                    searchable: false,
                    render: function(data, type, row){
                        var text = '';
                        text += '<a href="#" ';
                        text += 'class="tp_rel" ';
                        text += 'data-dest="card" ';
                        text += 'data-id="' + row.id + '" ';
                        text += 'data-text="' + row.label + '" ';
                        text += 'data-toggle="tooltip" ';
                        text += 'data-html="true" ';
                        text += 'title="' + row.cards.text + '">';
                        text += row.cards.count ;
                        text += '</a>';
                        return text;
                    },
                    targets: 2,
                }, {
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row){
                        var text = '';
                        text += '<button class="btn btn-sm " '; 
                        text += 'title="Edit label" '; 
                        text += 'data-toggle="modal" '; 
                        text += 'data-target="#tp_modal_label" '; 
                        text += 'data-op="edit">';
                        text += '<i class="fa fa-edit"></i>';
                        text += '</button>';
                        text += '<button class="btn btn-sm " '; 
                        text += 'title="Clone label" '; 
                        text += 'data-toggle="modal" '; 
                        text += 'data-target="#tp_modal_label" '; 
                        text += 'data-op="clone">';
                        text += '<i class="fa fa-clone"></i>';
                        text += '</button>';
                        text += '<button class="btn btn-sm btn-danger" '; 
                        text += 'title="Delete label" '; 
                        text += 'data-toggle="modal" '; 
                        text += 'data-target="#tp_modal_label_delete">';
                        text += '<i class="fa fa-trash"></i>';
                        text += '</button>';
                        return text;
                    },
                    targets: 3,
                }
            ],
            columns:[
                { data: 'id' },
                { data: 'label' },
                { data: 'cards' },
                { data: 'action' },
            ],
            initComplete: function(settings, json, card_id) {
                $('#tp_label').show();
            },
        });
    }
}

function refreshDatatableCard(){

    if ('card' in tp.dt){
        tp.dt.card.ajax.reload(function() {
            $('#tp_card').show();
        });
    } else {
        tp.dt.card = $('#tp_card_table').DataTable({
            order: [[ 0, "desc" ]],
            processing: true,
            serverSide: true,
            serverMethod: 'post',
            ajax: {
                url: '/datatable',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: {
                    tpApp : 'card',
                    tpUserData: function() { return tp.state.filter.user; },
                    tpLabelData: function() { return tp.state.filter.label; },
                    tpCardData: function() { return tp.state.filter.card; },
                    tpExampleData: function() { return tp.state.filter.example; },
                }
            },
            columnDefs: [{
                className: "text-center",
                targets: [5, 6, 7],
            }, { 
                searchable: false,
                visible: false, 
                targets: 0,
            }, {
                render: function (data, type, row){
                    var text = '';
                    if (row.symbol.url.length > 0){
                        text += '<button class="btn, btn-sm btn-primary tp-audio" data-path="' + row.symbol.url + '">';
                        text += '<i class="fa fa-play"></i>';
                        text += '</button>';
                    }
                    text += '<span data-toggle="tooltip" title="' + row.id + '">' + row.symbol.symbol + '</span>';
                    return text;
                },
                targets: 1,
            }, {
                searchable: false,
                render: function(data, type, row){
                    var text = '';
                    text += '<a href="#" ';
                    text += 'class="tp_rel" ';
                    text += 'data-dest="label" ';
                    text += 'data-id="' + row.id + '" ';
                    text += 'data-text="' + row.symbol.symbol + '" ';
                    text += 'data-toggle="tooltip" ';
                    text += 'data-html="true" ';
                    text += 'title="' + row.labels.text + '">';
                    text += row.labels.count ;
                    text += '</a>';
                    return text;
                },
                targets: 5,
            }, {
                searchable: false,
                render: function(data, type, row){
                    var text = '';
                    text += '<a href="#" ';
                    text += 'class="tp_rel" ';
                    text += 'data-dest="example" ';
                    text += 'data-id="' + row.id + '" ';
                    text += 'data-text="' + row.symbol.symbol + '" ';
                    text += 'data-toggle="tooltip" ';
                    text += 'data-html="true" ';
                    text += 'title="' + row.examples.text + '">';
                    text += row.examples.count;
                    text += '</a>';
                    return text;
                },
                targets: 6,
            }, {
                orderable: false,
                searchable: false,
                render: function(data, type, row){
                    var text = '';
                    text += '<button class="btn btn-sm " '; 
                    text += 'title="Edit card" '; 
                    text += 'data-toggle="modal" '; 
                    text += 'data-target="#tp_modal_card" '; 
                    text += 'data-op="edit">';
                    text += '<i class="fa fa-edit"></i>';
                    text += '</button>';
                    text += '<button class="btn btn-sm " '; 
                    text += 'title="Clone card" '; 
                    text += 'data-toggle="modal" '; 
                    text += 'data-target="#tp_modal_card" '; 
                    text += 'data-op="clone">';
                    text += '<i class="fa fa-clone"></i>';
                    text += '</button>';
                    text += '<button class="btn btn-sm btn-danger" '; 
                    text += 'title="Delete card" '; 
                    text += 'data-toggle="modal" '; 
                    text += 'data-target="#tp_modal_card_delete">';
                    text += '<i class="fa fa-trash"></i>';
                    text += '</button>';
                    return text;
                },
                targets: 7,
            }
            ],
            columns:[
                { data: 'id' },
                { data: 'symbol' },
                { data: 'pinyin' },
                { data: 'translation' },
                { data: 'comment' },
                { data: 'labels' },
                { data: 'examples' },
                { data: 'action' },
            ],
            initComplete: function(settings, json, card_id) {
                $('#tp_card').show();
            },
        });
    }
}

function refreshDatatableExample(){

    if ('example' in tp.dt){
        tp.dt.example.ajax.reload(function() {
            $('#tp_example').show();
        });
    } else {
        tp.dt.example = $('#tp_example_table').DataTable({
            order: [[ 0, "desc" ]],
            processing: true,
            serverSide: true,
            serverMethod: 'post',
            ajax: {
                url: '/datatable',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: {
                    tpApp : 'example',
                    tpUserData: function() { return tp.state.filter.user; },
                    tpLabelData: function() { return tp.state.filter.label; },
                    tpCardData: function() { return tp.state.filter.card; },
                    tpExampleData: function() { return tp.state.filter.example; },
                }
            },
            columnDefs: [{
                className: "text-center",
                targets: [3, 4],
            }, { 
                searchable: false,
                visible: false, 
                targets: 0,
            }, {
                render: function (data, type, row){
                    var text = '';
                    if (row.example.url.length > 0){
                        text += '<button class="btn, btn-sm btn-primary tp-audio" data-path="' + row.example.url + '">';
                        text += '<i class="fa fa-play"></i>';
                        text += '</button>';
                    }
                    text += '<span data-toggle="tooltip" title="' + row.id + '">' + row.example.example + '</span>';
                    return text;
                },
                targets: 1,
            }, {
                searchable: false,
                render: function(data, type, row){
                    var text = '';
                    text += '<a href="#" ';
                    text += 'class="tp_rel" ';
                    text += 'data-dest="card" ';
                    text += 'data-id="' + row.id + '" ';
                    text += 'data-text="' + row.example.example + '" ';
                    text += 'data-toggle="tooltip" ';
                    text += 'data-html="true" ';
                    text += 'title="' + row.cards.text + '">';
                    text += row.cards.count ;
                    text += '</a>';
                    return text;
                },
                targets: 3,
            }, {
                orderable: false,
                searchable: false,
                render: function(data, type, row){
                    var text = '';
                    text += '<button class="btn btn-sm " '; 
                    text += 'title="Edit card" '; 
                    text += 'data-toggle="modal" '; 
                    text += 'data-target="#tp_modal_card" '; 
                    text += 'data-op="edit">';
                    text += '<i class="fa fa-edit"></i>';
                    text += '</button>';
                    text += '<button class="btn btn-sm " '; 
                    text += 'title="Clone card" '; 
                    text += 'data-toggle="modal" '; 
                    text += 'data-target="#tp_modal_card" '; 
                    text += 'data-op="clone">';
                    text += '<i class="fa fa-clone"></i>';
                    text += '</button>';
                    text += '<button class="btn btn-sm btn-danger" '; 
                    text += 'title="Delete card" '; 
                    text += 'data-toggle="modal" '; 
                    text += 'data-target="#tp_modal_card_delete">';
                    text += '<i class="fa fa-trash"></i>';
                    text += '</button>';
                    return text;
                },
                targets: 4,
            }
            ],
            columns:[
                { data: 'id' },
                { data: 'example' },
                { data: 'translation' },
                { data: 'cards' },
                { data: 'action' },
            ],
            initComplete: function(settings, json, card_id) {
                $('#tp_example').show();
            },
        });
    }
}

function refreshDatatableQuiz(){
    if ('quiz' in tp.dt){
        tp.dt.quiz.ajax.reload();
    } else {
        tp.dt.quiz = $('#tp_quiz_table').DataTable({
            paging: false,
            searching: false,
            order: [[ 0, "desc" ]],
            processing: true,
            serverSide: true,
            serverMethod: 'post',
            ajax: {
                url: '/datatable',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            },
            columnDefs: [{
                visible: false, 
                targets: 0,
            }, {
                render: function (data, type, row){
                    var text = '';
                    if (row.example.url.length > 0){
                        text += '<button class="btn, btn-sm btn-primary tp-audio" data-path="' + row.example.url + '">';
                        text += '<i class="fa fa-play"></i>';
                        text += '</button>';
                    }
                    text += '<span data-toggle="tooltip" title="' + row.id + '">' + row.example.example + '</span>';
                    return text;
                },
                targets: 1,
            }
            ],
            columns:[
                { data: 'id' },
                { data: 'example' },
                { data: 'translation' },
            ],
            initComplete: function(settings, json, example_id) {
                update_html();
            }
        });
    }
}

function initUserSelect(){

    // for user selection
    $('.tp_user').select2({
        dataType: 'json',
        minimumResultsForSearch: -1,
        ajax: {
            url:'/autocomplete',
            data: function(params) {
                return {
                    'searchData': params.term,
                    'selectType': 'user',
                    'userData': tp.state.filter.user,
                }
            },
            processResults: function(data){
                return { 
                    results: data
                };
            }
        },
        cache: true,
    });

    $("#tp_user").show();
}

function initFilterSelect(){

    // for filter selection
    $('.tp_filter').select2({
        width: '20%',
        dataType: 'json',
        minimumInputLength: 0,
        ajax: {
            url:'/autocomplete',
            data: function(params) {
                return {
                    'searchData': params.term,
                    'selectType': $(this).data('type'),
                    'userData' : tp.state.filter.user,
                }
            },
            processResults: function(data){
                return { 
                    results: data
                };
            }
        },
        cache: true,
    });


    $(".tp_filter").show();
}

function refreshFilter(items){

    ['user', 'label', 'card', 'example'].forEach(function(item) {
        if (items.includes(item)){
            var el = $('#tp_filter_' + item);
            var arr = JSON.parse(tp.state.filter[item]);
            el.val(null).trigger('change', true);
            for (var i = 0; i < arr.length; i++){
                var option = new Option(arr[i].text, arr[i].id, true, true );
                el.append(option);
            }
        }
    });

}

function clearFilters(){
    ['#tp_filter_label', '#tp_filter_card', '#tp_filter_example'].forEach( function (el) {
        $(el).val(null).trigger('change', true);
    });
}

function populate_quiz(){

    // populate page
    if (!state_data.tp_quiz_data){
        return;
    }
    var total = state_data.tp_quiz_data.stats.total;
    var remaining = state_data.tp_quiz_data.stats.remaining;
    $('#tp-stats').text('(' + remaining + '/' + total + ')');

    $('.tp-toggle-symbol').text(state_data.tp_quiz_data.symbol);
    $('.tp-toggle-pinyin').text(state_data.tp_quiz_data.pinyin);
    $('.tp-toggle-translation').text(state_data.tp_quiz_data.translation);
    $('.tp-toggle-example').text(state_data.tp_quiz_data.example);
    $('.tp-toggle-comment').text(state_data.tp_quiz_data.comment);
    $('.tp-toggle-label').text(state_data.tp_quiz_data.labels.join());
    if (state_data.tp_quiz_data.url == ''){
        $('.tp-has-audio').hide();
        $('.tp-no-audio').show();
    } else {
        $('.tp-has-audio').show();
        $('.tp-no-audio').hide();
    }

    // display card or not
    if (state_ids.tp_quiz_id == null){
        $('#tp_quiz .tp-has-card').hide();
        $('#tp_quiz .tp-no-card').show();
    } else {
        $('#tp_quiz .tp-has-card').show();
        $('#tp_quiz .tp-no-card').hide();
    }

    // update quiz display status
    $('#tp-show-hide').text(state_ids.tp_quiz_state);

}

function init_quiz(){


    return;
    populate_quiz();

    // display item or not
    $('#tp_quiz [data-state="on"]').show();
    $('#tp_quiz [data-state="off"]').show();
    if (state_ids.tp_quiz_shown.length > 0){
        $('.' + state_ids.tp_quiz_shown.join(', .')).attr('data-state', 'on');
    }

    // update fields shown
    refresh_all();
}

function update_html(){

    // update visible app
    $('#tp_label, #tp_card, #tp_example, #tp_quiz').not('tp_' + state_ids.tp_app).hide();
    $('#tp_' + state_ids.tp_app).show();


    // update navigation link
    $('.tp_link[data-app="' + state_ids.tp_app + '"]').parent().addClass('active');

}

function refresh_quiz(){

    $('#tp_quiz [data-state="on"]').show();

    if (state_ids.tp_quiz_state == 'Hide') {
        $('#tp_quiz [data-state="off"]').show();
    } else {
        $('#tp_quiz [data-state="off"]').hide();
    }

    // playback
    if (false){
        $(".tp-audio").trigger('click');
    }
}

function switchToLabel(){
    refreshDatatableLabel();
}

function switchToCard(){
    refreshDatatableCard();
}

function switchToExample(){
    refreshDatatableExample();
}

function refreshAll(items){

    if (items.includes('app')){
        console.log('app');

        switch (tp.state.app){
            case 'label':
                switchToLabel();
                break;
            case 'card':
                switchToCard();
                break;
            case 'example':
                switchToExample();
                break;
            case 'quiz':
                switchToQuiz();
                break;
        }

        // update nav bar
        $('.tp_link').not('[data-app="' + tp.state.app + '"]').parent().removeClass('active');
        $('.tp_link[data-app="' + tp.state.app + '"]').parent().addClass('active');

        // hide all other applications
        $('#tp_label, #tp_card, #tp_example, #tp_quiz').not('tp_' + tp.state.app).hide();

    }

    if (items.includes('data')){
        switch (tp.state.app){
            case 'label':
                refreshDatatableLabel();
                break;
            case 'card':
                refreshDatatableCard();
                break;
            case 'example':
                refreshDatatableExample();
                break;
            case 'quiz':
                refreshDatatableQuiz();
                break;
        }
    }

    // update filter content
    refreshFilter(items);

    var data = JSON.stringify(tp.state);
    sessionStorage.setItem('flashcard', data);
    console.log(tp);
            

}

function getInitState(){

    // Initialize state
    var tmp = sessionStorage.getItem('flashcard');
    tmp = null;
    if (tmp == null){

        tp.state = {};
        tp.state.app = "label";

        tp.state.filter = {};
        tp.state.filter.label = JSON.stringify([]);
        tp.state.filter.card = JSON.stringify([]);
        tp.state.filter.example = JSON.stringify([]);
        tp.state.filter.user = JSON.stringify($('#tp-const').data('user-data'));

        tp.state.quiz = {};
        tp.state.quiz.data = null;
        tp.state.quiz.state = "Show";
        tp.state.quiz.visibleFields = [];

        console.log("initial state created");

    } else {
        tp.state = JSON.parse(tmp);
        console.log("initial state retrieved");
    }

}

function initCanvas() {

    var canvas = document.querySelector('#tp-canvas');
    ctx = canvas.getContext("2d");

    // prevent canvas stretching by setting the canvas size to the element's size
    canvas.width = canvas.offsetWidth;
    canvas.height = canvas.offsetHeight;

    // add touch event handler (for tablets)
    canvas.addEventListener("touchstart", function (e) {
        touchPressed = true;
        var touch = e.touches[0];
        x = touch.pageX - $(this).offset().left;
        y = touch.pageY - $(this).offset().top;
        drawTouchCanvas(x, y, false);
    });

    canvas.addEventListener("touchmove", function (e) {
        if (touchPressed) {
            var touch = e.touches[0];
            x = touch.pageX - $(this).offset().left;
            y = touch.pageY - $(this).offset().top;
            drawTouchCanvas(x, y, true);
        }
    });

    canvas.addEventListener("touchend", function (e) {
        touchPressed = false;
    });

    canvas.addEventListener("touchcancel", function (e) {
        touchPressed = false;
    });

    // add mouse event handler
    $('#tp-canvas').mousedown(function (e) {
        mousePressed = true;
        x = e.pageX - $(this).offset().left;
        y = e.pageY - $(this).offset().top;
        drawMouseCanvas(x, y, false);
    });

    $('#tp-canvas').mousemove(function (e) {
        if (mousePressed) {
            x = e.pageX - $(this).offset().left;
            y = e.pageY - $(this).offset().top;
            drawMouseCanvas(x, y, true);
        }
    });

    $('#tp-canvas').mouseup(function (e) {
        mousePressed = false;
    });

    $('#tp-canvas').mouseleave(function (e) {
        mousePressed = false;
    });
}

function drawTouchCanvas(x, y, isDown) {
    if (isDown) {
        ctx.beginPath();
        ctx.strokeStyle = 'black';
        ctx.lineWidth = 1;
        ctx.lineJoin = "round";
        ctx.moveTo(lastTouchX, lastTouchY);
        ctx.lineTo(x, y);
        ctx.closePath();
        ctx.stroke();
    }
    lastTouchX = x; lastTouchY = y;
}

function drawMouseCanvas(x, y, isDown) {
    if (isDown) {
        ctx.beginPath();
        ctx.strokeStyle = 'black';
        ctx.lineWidth = 1;
        ctx.lineJoin = "round";
        ctx.moveTo(lastMouseX, lastMouseY);
        ctx.lineTo(x, y);
        ctx.closePath();
        ctx.stroke();
    }

    lastMouseX = x; lastMouseY = y;
}
