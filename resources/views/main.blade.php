@extends ('app')

@push ('css')
@endpush

@section ('content')

    <div class="container shadow mb-5 mt-5 pt-2 pb-2 bg-light rounded">
        <div class="mb-15">
            <div class="container">
                <div class="row">

                    <button 
                        id="tp_filter_clear"
                        title="Clear Filter" 
                        class="btn btn-primary">
                        <i class="fa fa-filter"></i>
                    </button>

                    <select 
                       id="tp_filter_label" 
                       name="tp_filter_label[]" 
                       class="tp_filter" 
                       data-type="label"
                       data-placeholder="No labels selected"
                       style="display: none;width: 20%;"
                       multiple>
                    </select>

                    <select 
                       id="tp_filter_card" 
                       name="tp_filter_card[]" 
                       class="tp_filter" 
                       data-type="card"
                       data-placeholder="No cards selected" 
                       style="display: none;width: 20%;"
                       multiple>
                    </select>

                    <select 
                       id="tp_filter_example" 
                       name="tp_filter_example[]" 
                       class="tp_filter" 
                       data-type="example"
                       data-placeholder="No examples selected" 
                       style="display: none;width: 20%;"
                       multiple>
                    </select>

                    {{-- Separator --}}
                    <div style="margin-left:auto"></div>

                    <select 
                        id="tp_user" 
                        name="tp_user" 
                        class="tp_user text-right" 
                        style="margin-left:auto"
                        data-width="fit"
                        title="No user selected">
                    </select>

                </div>

                <div class="row">
                    <div class="col-12">
                        @if ($message = Session::get('success'))
                        <div class="alert alert-success alert-block">
                            <button type="button" class="close" data-dismiss="alert">x</button>
                            <span>{{ $message }}</span>
                        </div>
                        @endif
                        @if ($message = Session::get('error'))
                        <div class="alert alert-danger alert-block">
                            <button type="button" class="close" data-dismiss="alert">x</button>
                            <span>{{ $message }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div id="tp_label" class="container shadow mb-5 mt-5 bg-light rounded" style="display: none;">
        @include ('labels')
    </div>
    <div id="tp_card" class="container shadow mb-5 mt-5 bg-light rounded" style="display: none;">
        @include ('cards')
    </div>
    <div id="tp_example" class="container shadow mb-5 mt-5 bg-light rounded" style="display: none;">
        @include ('examples')
    </div>
    <div id="tp_quiz" class="container shadow mb-5 mt-5 bg-light rounded" style="display: none;">
        @include ('quiz')
    </div>

    @include ('labels.edit')
    @include ('labels.delete')

    @include ('cards.edit')
    @include ('cards.delete')

    @include ('examples.edit')
    @include ('examples.delete')

@endsection

@push ('scripts')
    <script>

        var state_ids = null;
        var state_data = null;
        var table = [];

        $(document).ready( function () {

            // initialize state and filters
            init_state();

            // initialize tooltip
            $('[data-toggle="tooltip"]').tooltip();

            
            $(document).on('click', '.fc-audio', function (event) {

                var src = $(this).data('path');
                var audio = new Audio(src);
                audio.load();
                audio.play();
                return;

            });

            $(document).on('click', '#tp_filter_clear', function (event) {
                ['#tp_filter_label', '#tp_filter_card', '#tp_filter_example'].forEach( function (el) {
                    if ($(el).val().length > 0){
                        $(el).val([]).trigger('change');
                    }
                });
            });

            $(document).on('click', '.tp_rel', function (event) {
                var filter = $('#tp_filter_' + state_ids.tp_app);
                var option = new Option($(this).data('text'), $(this).data('id'), true, true );
                state_ids.tp_app = $(this).data('dest');
                filter.append(option).trigger('change');
            });

            $(document).on('change', '#tp_filter_label', function (event) {
                state_ids.tp_label_ids = $(this).val();
                store_state();
            });

            $(document).on('change', '#tp_filter_card', function (event) {
                state_ids.tp_card_ids = $(this).val();
                store_state();
            });

            $(document).on('change', '#tp_filter_example', function (event) {
                state_ids.tp_example_ids = $(this).val();
                store_state();
            });

            $(document).on('change', '#tp_user', function (event) {
                state_ids.tp_user_id = $(this).val();
                store_state();
            });

            $(document).on('click', '.tp_link', function (event) {
                state_ids.tp_app = $(this).data('app');
                store_state();
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

        function init_datatable_label(){

            return $('#tp_label_table').DataTable({
                order: [[ 0, "desc" ]],
                processing: true,
                serverSide: true,
                serverMethod: 'post',
                ajax: {
                    url: '/pagination',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
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

                    var card_id = $('#tp_const').data('card_id');

                    var row = this.api().row(function ( idx, data, node ) {
                        return data[0] == card_id;
                    } );

                    if (row.length == 1) {
                        row.show().draw(false); 
                    }

                    update_html();
                },
            });
        }

        function init_datatable_card(){

            return $('#tp_card_table').DataTable({
                order: [[ 0, "desc" ]],
                processing: true,
                serverSide: true,
                serverMethod: 'post',
                ajax: {
                    url: '/pagination',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                },
                columnDefs: [{
                        className: "text-center",
                        targets: "_all",
                    }, { 
                        searchable: false,
                        visible: false, 
                        targets: 0,
                    }, {
                        render: function (data, type, row){
                            var text = '';
                            if (row.symbol.url.length > 0){
                                text += '<button class="btn, btn-sm btn-primary fc-audio" data-path="' + row.symbol.url + '">';
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

                    var card_id = $('#tp_const').data('card_id');

                    var row = this.api().row(function ( idx, data, node ) {
                        return data[0] == card_id;
                    } );

                    if (row.length == 1) {
                        row.show().draw(false); 
                    }

                    update_html();
                },
            });
        }

        function init_datatable_example(){

            return $('#tp_example_table').DataTable({
                order: [[ 0, "desc" ]],
                processing: true,
                serverSide: true,
                serverMethod: 'post',
                ajax: {
                    url: '/pagination',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
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
                                text += '<button class="btn, btn-sm btn-primary fc-audio" data-path="' + row.example.url + '">';
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

                    var card_id = $('#tp_const').data('card_id');

                    var row = this.api().row(function ( idx, data, node ) {
                        return data[0] == card_id;
                    } );

                    if (row.length == 1) {
                        row.show().draw(false); 
                    }

                    update_html();
                },
            });
        }

        function init_datatable_quiz(){
            return $('#tp_quiz_table').DataTable({
                paging: false,
                searching: false,
                order: [[ 0, "desc" ]],
                processing: true,
                serverSide: true,
                serverMethod: 'post',
                ajax: {
                    url: '/pagination',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                },
                columnDefs: [{
                        visible: false, 
                        targets: 0,
                    }, {
                        render: function (data, type, row){
                            var text = '';
                            if (row.example.url.length > 0){
                                text += '<button class="btn, btn-sm btn-primary fc-audio" data-path="' + row.example.url + '">';
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

        function init_filter_select(){

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
                            'searchType': $(this).data('type'),
                        }
                        return query;
                    },
                    processResults: function(data){
                        return { 
                            results: data
                        };
                    }
                },
                cache: true,
            });

            // populate filters
            for (var i = 0; i < state_ids.tp_label_ids.length; i++){
                var option = new Option(state_data.tp_label_data[i], state_ids.tp_label_ids[i], true, true );
                $('#tp_filter_label').append(option);
            }

            for (var i = 0; i < state_ids.tp_card_ids.length; i++){
                var option = new Option(state_data.tp_card_data[i], state_ids.tp_card_ids[i], true, true );
                $('#tp_filter_card').append(option); //.trigger('change');
            }

            for (var i = 0; i < state_ids.tp_example_ids.length; i++){
                var option = new Option(state_data.tp_example_data[i], state_ids.tp_example_ids[i], true, true );
                $('#tp_filter_example').append(option); //.trigger('change');
            }

            $(".tp_filter").show();
        }

        function init_user_select(){

            // for user selection
            $('.tp_user').select2({
                dataType: 'json',
                minimumResultsForSearch: -1,
                ajax: {
                    url:'/autocomplete',
                    data: function(params) {
                        return {
                            'searchData': params.term,
                            'searchType': 'user',
                        }
                        return query;
                    },
                    processResults: function(data){
                        return { 
                            results: data
                        };
                    }
                },
                cache: true,
            });

            var option = new Option(state_data.tp_user_data, state_ids.tp_user_id, true, true );
            $('#tp_user').append(option); //.trigger('change');

            $(".tp_user").show();

        }

        function init_datatable(){

            switch(state_ids.tp_app){
            case 'label':
                table['label'] = init_datatable_label();
                break;
            case 'card':
                table['card'] = init_datatable_card();
                break;
            case 'example':
                table['example'] = init_datatable_example();
                break;
            case 'quiz':
                table['quiz'] = init_datatable_quiz();
                break;
            }

        }

        function init_state(){
            $.ajax({
                type: 'get',
                url: '/session',
                success: function(data, status){

                    state_ids = data.tp_state_ids;
                    state_data = data.tp_state_data;

                    $('#tp_' + state_ids.tp_app).show();
                    
                    init_filter_select();

                    init_user_select();

                    init_datatable();

                },
            })
        }

        function store_state(){
            $.ajax({
                type: 'post',
                url: '/session',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: {
                    tp_data: state_ids,
                }, 
                success: function(data, status){
                    if (data.tp_status == 'success'){

                        if (state_ids.tp_app in table){
                            table[state_ids.tp_app].ajax.reload(function (data){ 
                                update_html();
                            });
                        } else {
                            init_datatable();
                        }
                    } else {
                        console.log('error');
                    }
                },
            })

        }

        function update_html(){

            // update visible app
            $('#tp_label, #tp_card, #tp_example, #tp_quiz').not('tp_' + state_ids.tp_app).hide();
            $('#tp_' + state_ids.tp_app).show();


            // update navigation link
            $('.tp_link').not('[data-app="' + state_ids.tp_app + '"]').parent().removeClass('active');
            $('.tp_link[data-app="' + state_ids.tp_app + '"]').parent().addClass('active');

        }


    </script>
@endpush

