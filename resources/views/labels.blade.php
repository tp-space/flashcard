@extends('app')

@push ('css')
@endpush

@section ('content')
<div 
    data-label_id="{{ Session::get("label_id", "-1") }}"
    id="tp_const">
</div>

<div id="tp_content_loading" class="container shadow mb-5 mt-5 bg-light rounded">
    loading
</div>
<div id="tp_content" class="container shadow mb-5 mt-5 bg-light rounded" style="display: none;">

    <div class="row mt-3 mb-3">
        <div class="col-md-6 text-md-left"><h1>Labels</h1></div>
        <div class="col-md-6 text-md-right">

            <!-- Button New -->
            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#tp_modal_label" data-op="new">
                <i class="fa fa-plus"></i>
            </button>

        </div>
    </div>

    <table id="tp_label_table" class="display" style="width: 100%;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Label name</th>
                <th class="text-center">Cards</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($labels as $label)
            <tr id="tp_tr_{{ $label->id }}" data-id="{{ $label->id }}">
                <td tp_item="tp_id">{{ $label->id }}</td>
                <td tp_item="tp_label" data-toggle="tooltip" title="{{ $label->id }}">{{ $label->label }}</td>
                <td tp_item="tp_cards" tp_value="{{ $label->cards->pluck('id') }}" class="text-center">
                    <a 
                        href="/filter/label/{{ $label->id }}/cards"
                        data-toggle="tooltip"
                        data-html="true"
                        title="{{ implode('<br>', $label->cards->pluck('symbol')->toArray()) }}">

                        {{ $label->cards->count() }}

                     </a>
                </td>
                <td class="text-center">
                    <button class="btn btn-sm" data-toggle="modal" data-target="#tp_modal_label" data-op="edit">
                        <i class="fa fa-edit"></i>
                    </button>
                    <button class="btn btn-sm" data-toggle="modal" data-target="#tp_modal_label" data-op="clone">
                        <i class="fa fa-clone"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#tp_modal_label_delete">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>

<!-- The Modal Create/Edit/clone -->
<div class="modal" id="tp_modal_label">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title" id="tp_modal_title">TBD</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">

                <form id="tp_modal_label_form" action="/labels" method="POST" class="was-validated">

                    @csrf
                    <input type="hidden" id="_method_change" name="_method" value="TBD">

                    <div class="form-group">
                        <label for="tp_label">Label:</label>
                        <input type="text" class="form-control" id="tp_label" placeholder="Enter label" name="tp_label" required>
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
                <button type="submit" class="btn btn-primary" form="tp_modal_label_form">Submit</button>
                <button type="button" class="btn btn-standard" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

<!-- The Modal Delete -->
<div class="modal" id="tp_modal_label_delete">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Delete Label</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">

                <form id="tp_modal_label_delete_form" action="/labels" method="post">
                    @csrf
                    @method('delete')
                    <p id="tp_modal_label_delete_text"></p>
                </form>

            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="submit" class="btn btn-danger" form="tp_modal_label_delete_form">Delete</button>
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
            var label_id = $('#tp_const').data('label_id');
            $('#tp_tr_' + label_id).addClass('selected');

            // initialize dropdown for labels
            $('#tp_cards').selectpicker();

            // initialize tooltip
            $('[data-toggle="tooltip"]').tooltip();

            var table = $('#tp_label_table').DataTable({
                order: [[ 0, "desc" ]],
                columnDefs: [{ visible: false, targets: 0 }],
                initComplete: function(settings, json, label_id) {

                    var label_id = $('#tp_const').data('label_id');

                    var row = this.api().row(function ( idx, data, node ) {
                        return data[0] == label_id;
                    } );

                    if (row.length == 1) {
                        row.show().draw(false); 
                    }

                    $('#tp_content_loading').hide();
                    $('#tp_content').show();
                }
            });
        });

        $(document).on('shown.bs.modal', '#tp_modal_label', function (event) {

            // get operation from button that triggered the modal form
            var button = $(event.relatedTarget);
            console.log(button);
            var op = button.data('op');
            console.log(op);

            switch(op) {
            case "new":

                // configure modal form
                $('#tp_modal_title').html('Create Label');
                $('#_method_change').val('POST');

                // initialize modal form values
                $('#tp_modal_label #tp_label').val('');
                $('#tp_modal_label #tp_cards').val([]).change();

                break;

            case "edit":

                // get label id
                var el_tr = $(button).parent().parent();
                var id = el_tr.find('[tp_item="tp_id"]').html();

                // configure modal form
                $('#tp_modal_title').html('Edit Label');
                $('#_method_change').val('PUT');
                $('#tp_modal_label_form').attr('action', '/labels/' + id);

                // code block
                $('#tp_modal_label #tp_id').val(el_tr.find('[tp_item="tp_id"]').html());
                $('#tp_modal_label #tp_label').val(el_tr.find('[tp_item="tp_label"]').html());
                $('#tp_modal_label #tp_cards').val(JSON.parse(el_tr.find('[tp_item="tp_cards"]').attr('tp_value'))).change();

                break;

            case "clone":

                // configure modal form
                $('#tp_modal_title').html('Clone Label');
                $('#_method_change').val('POST');

                // code block
                var el_tr = $(button).parent().parent();
                $('#tp_modal_label #tp_label').val(el_tr.find('[tp_item="tp_label"]').html());
                $('#tp_modal_label #tp_cards').val(JSON.parse(el_tr.find('[tp_item="tp_cards"]').attr('tp_value'))).change();

                break;
            default:

                // code block
                assert(true);
            } 

        });

        $(document).on('shown.bs.modal', '#tp_modal_label_delete', function (event) {

            // get label id
            var button = $(event.relatedTarget);
            var el_tr = $(button).parent().parent();
            id = el_tr.find('[tp_item="tp_id"]').html();

            // update modal form content
            $('#tp_modal_label_delete_form').attr('action', '/labels/' + id);
            $('#tp_modal_label_delete_text').html('Do you really want to delete '  + id + '?');

        });
    </script>
@endpush

