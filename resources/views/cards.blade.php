@extends ('app')

@push ('css')
@endpush

@section ('content')

<div 
    data-card_id="{{ Session::get("card_id", "-1") }}"
    id="tp_const">
</div>

<div id="tp_content_loading" class="container shadow mb-5 mt-5 bg-light rounded">
    loading
</div>
<div id="tp_content" class="container shadow mb-5 mt-5 bg-light rounded" style="display: none;">
    <div class="row mt-3 mb-3">
        <div class="col-md-6 text-md-left"><h1>Cards</h1></div>
        <div class="col-md-6 text-md-right">

            <!-- Button New -->
            <button 
                type="button" 
                title="Add new card"
                class="btn btn-success" 
                data-toggle="modal" 
                data-target="#tp_modal_card" 
                data-op="new">
                <i class="fa fa-plus"></i>
            </button>

        </div>
    </div>
    <table id="tp_card_table" class="display" style="width:100%;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Symbol</th>
                <th>Pinyin</th>
                <th>Translation</th>
                <th>Comment</th>
                <th class="text-center">Labels</th>
                <th class="text-center">Examples</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($cards as $card)
            <tr id="tp_tr_{{ $card->id }}" data-id="{{ $card->id }}">
                <td tp_item="tp_id">{{ $card->id }}</td>
                <td tp_item="tp_symbol" data-toggle="tooltip" title="{{ $card->id }}">
                    @php ($audioPath = App\Http\Controllers\AudioController::getAudioFilePath(App\Http\Controllers\AudioController::CARD, $card->id))
                    @if (file_exists($audioPath['full']))
                    <button class="btn btn-sm btn-primary fc-audio" data-path="{{ $audioPath['url'] }}">
                        <i class="fa fa-play"></i>
                    </button>
                    @endif
                    {{ $card->symbol }}
                </td>
                <td tp_item="tp_pinyin">{{ $card->pinyin }}</td>
                <td tp_item="tp_translation">{{ $card->translation }}</td>
                <td tp_item="tp_comment">{{ $card->comment }}</td>
                <td tp_item="tp_labels" tp_value="{{ json_encode($card->labels->pluck('id')) }}" class="text-center"> 
                    <a href="/filter/card/{{ $card->id }}/labels"
                        data-toggle="tooltip"
                        data-html="true"
                        title="{{ implode('<br>', $card->labels->pluck('label')->toArray()) }}">
                        
                        {{ $card->labels->count() }}
                    
                    </a>
                </td>
                <td tp_item="tp_examples" tp_value="{{ json_encode($card->examples->pluck('id')) }}" class="text-center"> 
                    <a href="/filter/card/{{ $card->id }}/examples"
                        data-toggle="tooltip"
                        data-html="true"
                        title="{{ implode('<br>', $card->examples->pluck('example')->toArray()) }}">

                        {{ $card->examples->count() }}
                    
                    </a>
                </td>
                <td class="text-center">
                    <div class="btn-group">
                        <button class="btn btn-sm " 
                            title="Edit card" 
                            data-toggle="modal" 
                            data-target="#tp_modal_card" 
                            data-op="edit">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button 
                            class="btn btn-sm " 
                            title="Clone card" 
                            data-toggle="modal" 
                            data-target="#tp_modal_card" 
                            data-op="clone">
                            <i class="fa fa-clone"></i>
                        </button>
                        <button 
                            class="btn btn-sm btn-danger" 
                            title="Delete card" 
                            data-toggle="modal" 
                            data-target="#tp_modal_card_delete">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <audio id="fc-player" style="display: none;" src="" type="audio/mp3"></audio>

</div>


<!-- The Modal Create/Edit/clone -->
<div class="modal" id="tp_modal_card">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title" id="tp_modal_title">TBD</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">

                <form id="tp_modal_card_form" action="/cards" method="POST">

                    @csrf
                    <input type="hidden" id="_method_change" name="_method" value="TBD">

                    <div class="form-group">
                        <label for="tp_symbol">Symbol:</label>
                        <input type="text" class="form-control" id="tp_symbol" placeholder="Enter chinese character" name="tp_symbol" required>
                        <div class="invalid-feedback">Please fill out this field.</div>
                    </div>

                    <div class="form-group">
                        <label for="tp_pinyin">PinYin:</label>
                        <input type="text" class="form-control" id="tp_pinyin" placeholder="Enter pinyin" name="tp_pinyin" required>
                        <div class="invalid-feedback">Please fill out this field.</div>
                    </div>

                    <div class="form-group">
                        <label for="tp_translation">Translation:</label>
                        <input type="text" class="form-control" id="tp_translation" placeholder="Enter the translation" name="tp_translation" required>
                        <div class="invalid-feedback">Please fill out this field.</div>
                    </div>

                    <div class="form-group">
                        <label for="tp_comment">Comment:</label>
                        <input type="text" class="form-control" id="tp_comment" placeholder="Enter the comment" name="tp_comment">
                    </div>



                    <div class="form-group">
                        <label for="tp_labels">Labels:</label>
                        <select 
                            class="form-control selectpicker" 
                            id="tp_labels" 
                            name="tp_labels[]" 
                            title="No label selected" 
                            data-live-search="true" 
                            {{-- required --}} --}}
                            multiple>
                            @foreach ($filterLabels as $label)
                                <option value="{{ $label->id }}">{{ $label->label }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">Please select at least one entry.</div>
                    </div>

                    <div class="form-group">
                        <label for="tp_examples">Examples:</label>
                        <select 
                            class="form-control selectpicker" 
                            id="tp_examples" 
                            name="tp_examples[]" 
                            title="No example selected" 
                            data-live-search="true" 
                            {{-- required --}} --}}
                            multiple>
                            @foreach ($filterExamples as $example)
                                <option value="{{ $example->id }}">{{ substr($example->example,0,50) }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">Please select at least one entry.</div>
                    </div>

                </form>

            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" form="tp_modal_card_form">Submit</button>
                <button type="button" class="btn btn-standard" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

<!-- The Modal Delete -->
<div class="modal" id="tp_modal_card_delete">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Delete Card</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">

                <form id="tp_modal_card_delete_form" action="/cards" method="post">
                    @csrf
                    @method('delete')
                    <p id="tp_modal_card_delete_text"></p>
                </form>

            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="submit" class="btn btn-danger" form="tp_modal_card_delete_form">Delete</button>
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
            var card_id = $('#tp_const').data('card_id');
            $('#tp_tr_' + card_id).addClass('selected');

            // initialize dropdown for labels
            $('#tp_labels').selectpicker();
            $('#tp_examples').selectpicker();

            // initialize tooltip
            $('[data-toggle="tooltip"]').tooltip();

            var table = $('#tp_card_table').DataTable({
                order: [[ 0, "desc" ]],
                columnDefs: [{ visible: false, targets: 0 }],
                initComplete: function(settings, json, card_id) {

                    var card_id = $('#tp_const').data('card_id');

                    var row = this.api().row(function ( idx, data, node ) {
                        return data[0] == card_id;
                    } );

                    if (row.length == 1) {
                        row.show().draw(false); 
                    }

                    $('#tp_content_loading').hide();
                    $('#tp_content').show();
                }
            });
            
        } );


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

                // get card id
                var el_tr = $(button).closest('tr');
                var id = el_tr.data('id');

                // configure modal form
                $('#tp_modal_title').html('Edit Card');
                $('#_method_change').val('PUT');
                $('#tp_modal_card_form').attr('action', '/cards/' + id);

                // code block
                $('#tp_modal_card #tp_symbol').val(el_tr.find('[tp_item="tp_symbol"]').html());
                $('#tp_modal_card #tp_pinyin').val(el_tr.find('[tp_item="tp_pinyin"]').html());
                $('#tp_modal_card #tp_translation').val(el_tr.find('[tp_item="tp_translation"]').html());
                $('#tp_modal_card #tp_comment').val(el_tr.find('[tp_item="tp_comment"]').html());
                $('#tp_modal_card #tp_labels').val(JSON.parse(el_tr.find('[tp_item="tp_labels"]').attr('tp_value'))).change();
                $('#tp_modal_card #tp_examples').val(JSON.parse(el_tr.find('[tp_item="tp_examples"]').attr('tp_value'))).change();

                break;

            case "clone":

                // configure modal form
                $('#tp_modal_title').html('Clone Card');
                $('#_method_change').val('POST');

                // code block
                var el_tr = $(button).closest('tr');
                $('#tp_modal_card #tp_symbol').val(el_tr.find('[tp_item="tp_symbol"]').html());
                $('#tp_modal_card #tp_pinyin').val(el_tr.find('[tp_item="tp_pinyin"]').html());
                $('#tp_modal_card #tp_translation').val(el_tr.find('[tp_item="tp_translation"]').html());
                $('#tp_modal_card #tp_comment').val(el_tr.find('[tp_item="tp_comment"]').html());
                $('#tp_modal_card #tp_labels').val(JSON.parse(el_tr.find('[tp_item="tp_labels"]').attr('tp_value'))).change();
                $('#tp_modal_card #tp_examples').val(JSON.parse(el_tr.find('[tp_item="tp_examples"]').attr('tp_value'))).change();

                break;

            default:

                // code block
                assert(true);
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

        $(document).on('click', '.fc-audio', function (event) {

            var src = $(this).data('path');
            var audio = document.getElementById('fc-player');
            audio.setAttribute('src', src);
            audio.play();

        });

    </script>
@endpush

