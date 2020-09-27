@extends ('app')

@push ('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.css">
@endpush

@section ('content')

    <div 
        data-card_id="{{ Session::get("card_id", "0") }}"
        id="tp_const">
    </div>
    <div class="container shadow mb-5 mt-5 bg-light rounded">
test
    </div>

    <div class="container shadow mb-5 mt-5 bg-light rounded">
    <h1>Flashcards</h1>
    {{ Session::get('card_id', '0') }}

    <!-- Button to Open the Modal -->
    <button type="button" id="tp_card_new" class="btn btn-primary">New</button>

    <table id="tp_card" class="display">
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
        <tr id="tp_tr_{{ $card->id }}">
            <td tp_item="tp_id">{{ $card->id }}</td>
            <td tp_item="tp_symbol">{{ $card->symbol }}</td>
            <td tp_item="tp_pinyin">{{ $card->pinyin }}</td>
            <td tp_item="tp_translate">{{ $card->translate }}</td>
            <td tp_item="tp_example">{{ $card->example }}</td>
            <td>
                <button class="tp_card_edit_button">Edit</button>
                <button>delete</button>
                <button>clone</button>
            </td>
        </tr>
            @endforeach
            </tbody>
    </table>

</div>

<!-- The Modal -->
<div class="modal" id="tp_modal_card">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Create Card</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">

         <form action="/cards/create" class="was-validated">

              <!-- hidden variables -->
              <input type="hidden" id="tp_id" name="tp_id">

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

              <button type="submit" class="btn btn-primary">Submit</button>
        </form>

      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>



@endsection

@push ('scripts')
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/plug-ins/1.10.21/api/row().show().js"></script>
    <script>

        $(document).ready( function () {

            // Highlight the currently selected row
            var card_id = $('#tp_const').data('card_id');
            $('#tp_tr_' + card_id).addClass('selected');


            console.log('test');
            var table = $('#tp_card').DataTable({
                "initComplete": function(settings, json, card_id) {

                    var card_id = $('#tp_const').data('card_id');

                    var row = this.api().row(function ( idx, data, node ) {
                        return data[0] == card_id;
                    } );

                    if (row.length == 1) {
                        row.show().draw(false); 
                    }
                }
            });
            


            // create new card
            $('#tp_card_new').on('click', function(e) {

                // Prevent default behavior of click event
                e.preventDefault();

                // initialize modal form values
                $('#tp_modal_card #tp_id').val('');
                $('#tp_modal_card #tp_symbol').val('');
                $('#tp_modal_card #tp_pinyin').val('');
                $('#tp_modal_card #tp_translation').val('');

                // show modal form
                $('#tp_modal_card').modal('show');

            });

            // edit card
            $('.tp_card_edit_button').on('click', function(e) {

                // Prevent default behavior of click event
                e.preventDefault();

                // initialize modal form values
                
                var el_tr = $(this).parent().parent();
                $('#tp_modal_card #tp_id').val(el_tr.find('[tp_item="tp_id"]').html());
                $('#tp_modal_card #tp_symbol').val(el_tr.find('[tp_item="tp_symbol"]').html());
                $('#tp_modal_card #tp_pinyin').val(el_tr.find('[tp_item="tp_pinyin"]').html());
                $('#tp_modal_card #tp_translation').val(el_tr.find('[tp_item="tp_translation"]').html());

                // show modal form
                $('#tp_modal_card').modal('show');

            });

        } );
    </script>
@endpush

