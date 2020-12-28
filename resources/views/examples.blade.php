@extends('app')

@push ('css')
@endpush

@section ('content')
<div 
    data-example_id="{{ Session::get("example_id", "-1") }}"
    id="tp_const">
</div>

<div id="tp_content_loading" class="container shadow mb-5 mt-5 bg-light rounded">
    loading
</div>
<div id="tp_content" class="container shadow mb-5 mt-5 bg-light rounded" style="display: none;">

    <div class="row mt-3 mb-3">
        <div class="col-md-6 text-md-left"><h1>Examples</h1></div>
        <div class="col-md-6 text-md-right">

            <!-- Button New -->
            <button type="button" title="New example" class="btn btn-success" data-toggle="modal" data-target="#tp_modal_example" data-op="new">
                <i class="fa fa-plus"></i>
            </button>

        </div>
    </div>

    <table id="tp_example_table" class="display" style="width:100%;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Example</th>
                <th>Translation</th>
                <th class="text-center">Cards</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($examples as $example)
            <tr id="tp_tr_{{ $example->id }}" data-id="{{ $example->id }}">
                <td tp_item="tp_id">{{ $example->id }}</td>
                <td data-toggle="tooltip" title="{{ $example->id }}">
                    @php ($audioPath = App\Http\Controllers\AudioController::getAudioFilePath(App\Http\Controllers\AudioController::EXAMPLE, $example->id))
                    @if (file_exists($audioPath['fs']))
                    <button class="btn btn-sm btn-primary fc-audio" data-path="{{ $audioPath['url'] }}">
                        <i class="fa fa-play"></i>
                    </button>
                    @endif
                    <span tp_item="tp_example">{{ $example->example }}</span>
                </td>
                <td tp_item="tp_translation">{{ $example->translation }}</td>
                <td tp_item="tp_cards" tp_value="{{ $example->cards->pluck('id') }}" class="text-center">
                    <a 
                        href="/filter/example/{{ $example->id }}/cards"
                        data-toggle="tooltip"
                        data-html="true"
                        title="{{ implode('<br>', $example->cards->pluck('symbol')->toArray()) }}">

                        {{ $example->cards->count() }}

                    </a>
                </td>
                <td class="text-center">
                    <button class="btn btn-sm" title="Edit example" data-toggle="modal" data-target="#tp_modal_example" data-op="edit">
                        <i class="fa fa-edit"></i>
                    </button>
                    <button class="btn btn-sm" title="Clone example" data-toggle="modal" data-target="#tp_modal_example" data-op="clone">
                        <i class="fa fa-clone"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" title="Delete example" data-toggle="modal" data-target="#tp_modal_example_delete">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>

<!-- The Modal Create/Edit/clone -->
<div class="modal" id="tp_modal_example">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title" id="tp_modal_title">TBD</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">

                <form id="tp_modal_example_form" action="/examples" method="POST" class="was-validated">

                    @csrf
                    <input type="hidden" id="_method_change" name="_method" value="TBD">

                    <div class="form-group">
                        <label for="tp_example">Example:</label>
                        <input 
                               type="text" 
                               class="form-control" 
                               id="tp_example" 
                               placeholder="Enter the example" 
                               name="tp_example" 
                               required>
                        <div class="invalid-feedback">Please fill out this field.</div>
                    </div>

                    <div class="form-group">
                        <label for="tp_translation">Translation:</label>
                        <input 
                               type="text" 
                               class="form-control" 
                               id="tp_translation" 
                               placeholder="Enter the translation" 
                               name="tp_translation" 
                               required>
                        <div class="invalid-feedback">Please fill out this field.</div>
                    </div>

                    <div class="form-group">
                        <label for="tp_cards">Cards:</label>
                        <select 
                            class="form-control selectpicker" 
                            id="tp_cards" 
                            name="tp_cards[]" 
                            title="No card selected" 
                            data-live-search="true" 
                            {{-- required --}} --}}
                            multiple>
                            @foreach ($filterCards as $card)
                                <option value="{{ $card->id }}">{{ $card->symbol }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">Please select at least one entry.</div>
                    </div>


                </form>

            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" form="tp_modal_example_form">Submit</button>
                <button type="button" class="btn btn-standard" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

<!-- The Modal Delete -->
<div class="modal" id="tp_modal_example_delete">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Delete Example</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">

                <form id="tp_modal_example_delete_form" action="/examples" method="post">
                    @csrf
                    @method('delete')
                    <p id="tp_modal_example_delete_text"></p>
                </form>

            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="submit" class="btn btn-danger" form="tp_modal_example_delete_form">Delete</button>
                <button type="button" class="btn btn-standard" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>
@endsection

@push ('scripts')
    <script>
        $(document).ready( function () {

            // Highlight the currently selected row
            var example_id = $('#tp_const').data('example_id');
            $('#tp_tr_' + example_id).addClass('selected');

            // initialize dropdown for labels
            $('#tp_cards').selectpicker();

            // initialize tooltip
            $('[data-toggle="tooltip"]').tooltip();

            var table = $('#tp_example_table').DataTable({
                order: [[ 0, "desc" ]],
                columnDefs: [{ visible: false, targets: 0 }],
                initComplete: function(settings, json, example_id) {

                    var example_id = $('#tp_const').data('example_id');

                    var row = this.api().row(function ( idx, data, node ) {
                        return data[0] == example_id;
                    } );

                    if (row.length == 1) {
                        row.show().draw(false); 
                    }

                    $('#tp_content_loading').hide();
                    $('#tp_content').show();
                }
            });
            
        } );

        $(document).on('shown.bs.modal', '#tp_modal_example', function (event) {

            // get operation from button that triggered the modal form
            var button = $(event.relatedTarget);
            var op = button.data('op');

            switch(op) {
            case "new":

                // configure modal form
                $('#tp_modal_title').html('Create Example');
                $('#_method_change').val('POST');

                // initialize modal form values
                $('#tp_modal_example #tp_example').val('');
                $('#tp_modal_example #tp_translation').val('');
                $('#tp_modal_example #tp_cards').val([]).change();

                break;

            case "edit":

                // get example id
                var el_tr = $(button).closest('tr');
                var id = el_tr.data('id');

                // configure modal form
                $('#tp_modal_title').html('Edit Example');
                $('#_method_change').val('PUT');
                $('#tp_modal_example_form').attr('action', '/examples/' + id);

                // code block
                $('#tp_modal_example #tp_example').val(el_tr.find('[tp_item="tp_example"]').html());
                $('#tp_modal_example #tp_pinyin').val(el_tr.find('[tp_item="tp_pinyin"]').html());
                $('#tp_modal_example #tp_translation').val(el_tr.find('[tp_item="tp_translation"]').html());
                $('#tp_modal_example #tp_cards').val(JSON.parse(el_tr.find('[tp_item="tp_cards"]').attr('tp_value'))).change();

                break;

            case "clone":

                // configure modal form
                $('#tp_modal_title').html('Clone Example');
                $('#_method_change').val('POST');

                // code block
                var el_tr = $(button).closest('tr');
                $('#tp_modal_example #tp_example').val(el_tr.find('[tp_item="tp_example"]').html());
                $('#tp_modal_example #tp_pinyin').val(el_tr.find('[tp_item="tp_pinyin"]').html());
                $('#tp_modal_example #tp_translation').val(el_tr.find('[tp_item="tp_translation"]').html());
                $('#tp_modal_example #tp_cards').val(JSON.parse(el_tr.find('[tp_item="tp_cards"]').attr('tp_value'))).change();

                break;

            default:

                // code block
                assert(true);
            } 

        });

        $(document).on('shown.bs.modal', '#tp_modal_example_delete', function (event) {

            // get example id
            var button = $(event.relatedTarget);
            var el_tr = $(button).closest('tr');
            id = el_tr.data('id');

            // update modal form content
            $('#tp_modal_example_delete_form').attr('action', '/examples/' + id);
            $('#tp_modal_example_delete_text').html('Do you really want to delete '  + id + '?');

        });

    </script>
@endpush

