@extends ('app')

@push ('css')
@endpush

@section ('content')

    <div 
        data-card_id="{{ Session::get("card_id", "0") }}"
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
            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#tp_modal_card" data-op="new">
                <i class="fa fa-plus"></i>
            </button>

        </div>
    </div>
        <table id="tp_card_table" class="display">
    <thead>
        <tr>
            <th>ID</th>
            <th>Symbol</th>
            <th>Pinyin</th>
            <th>Translation</th>
            <th>Examples</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
            @foreach ($cards as $card)
        <tr id="tp_tr_{{ $card->id }}" data-id="{{ $card->id }}">
            <td tp_item="tp_id">{{ $card->id }}</td>
            <td tp_item="tp_symbol">{{ $card->symbol }}</td>
            <td tp_item="tp_pinyin">{{ $card->pinyin }}</td>
            <td tp_item="tp_translation">{{ $card->translation }}</td>
            <td tp_item="tp_example">{{ $card->example }}</td>
            <td>
                <button class="btn btn-sm" data-toggle="modal" data-target="#tp_modal_card" data-op="edit">
                    <i class="fa fa-edit"></i>
                </button>
                <button class="btn btn-sm" data-toggle="modal" data-target="#tp_modal_card" data-op="clone">
                    <i class="fa fa-clone"></i>
                </button>
                <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#tp_modal_card_delete">
                    <i class="fa fa-trash"></i>
                </button>
            </td>
        </tr>
            @endforeach
            </tbody>
    </table>

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

         <form id="tp_modal_card_form" action="/cards" method="POST" class="was-validated">

              @csrf

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
                <label for="tp_pinyin">Translation:</label>
                <input type="text" class="form-control" id="tp_translation" placeholder="Enter the translation" name="tp_translation" required>
                <div class="invalid-feedback">Please fill out this field.</div>
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


            var table = $('#tp_card_table').DataTable({
                "order": [[ 0, "desc" ]],
                "initComplete": function(settings, json, card_id) {

                    var card_id = $('#tp_const').data('card_id');

                    var row = this.api().row(function ( idx, data, node ) {
                        return data[0] == card_id;
                    } );

                    if (row.length == 1) {
                        row.show().draw(false); 
                    }

                    console.log('test');
                    $('#tp_content_loading').hide();
                    $('#tp_content').show();
                }
            });
            
        } );


        $(document).on('shown.bs.modal', '#tp_modal_card', function (event) {

            // get operation from button that triggered the modal form
            var button = $(event.relatedTarget);
            console.log(button);
            var op = button.data('op');
            console.log(op);

            switch(op) {
            case "new":

                // configure modal form
                $('#tp_modal_title').html('Create Card');
                $('tp_modal_card_form').attr('method', 'POST');

                // initialize modal form values
                $('#tp_modal_card #tp_id').val('');
                $('#tp_modal_card #tp_symbol').val('');
                $('#tp_modal_card #tp_pinyin').val('');
                $('#tp_modal_card #tp_translation').val('');

                break;

            case "edit":

                // get card id
                var el_tr = $(button).parent().parent();
                var id = el_tr.find('[tp_item="tp_id"]').html();

                // configure modal form
                $('#tp_modal_title').html('Edit Card');
                $('#tp_modal_card_form').attr('method', 'PUT');
                $('#tp_modal_card_form').attr('action', '/cards/' + id);

                // code block
                $('#tp_modal_card #tp_id').val(el_tr.find('[tp_item="tp_id"]').html());
                $('#tp_modal_card #tp_symbol').val(el_tr.find('[tp_item="tp_symbol"]').html());
                $('#tp_modal_card #tp_pinyin').val(el_tr.find('[tp_item="tp_pinyin"]').html());
                $('#tp_modal_card #tp_translation').val(el_tr.find('[tp_item="tp_translation"]').html());

                break;

            case "clone":

                // configure modal form
                $('#tp_modal_title').html('Clone Card');
                $('tp_modal_card_form').attr('method', 'POST');

                // code block
                var el_tr = $(button).parent().parent();
                $('#tp_modal_card #tp_symbol').val(el_tr.find('[tp_item="tp_symbol"]').html());
                $('#tp_modal_card #tp_pinyin').val(el_tr.find('[tp_item="tp_pinyin"]').html());
                $('#tp_modal_card #tp_translation').val(el_tr.find('[tp_item="tp_translation"]').html());
                break;
            default:

                // code block
                assert(true);
            } 

        });

        $(document).on('shown.bs.modal', '#tp_modal_card_delete', function (event) {

            // get card id
            var button = $(event.relatedTarget);
            var el_tr = $(button).parent().parent();
            id = el_tr.find('[tp_item="tp_id"]').html();

            // update modal form content
            $('#tp_modal_card_delete_form').attr('action', '/cards/' + id);
            $('#tp_modal_card_delete_text').html('Do you really want to delete '  + id + '?');

        });
    </script>
@endpush

