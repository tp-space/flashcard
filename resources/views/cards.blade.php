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
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#tp_modal_card" data-op="new">New</button>

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
        <tr id="tp_tr_{{ $card->id }}" data-id="{{ $card->id }}">
            <td tp_item="tp_id">{{ $card->id }}</td>
            <td tp_item="tp_symbol">{{ $card->symbol }}</td>
            <td tp_item="tp_pinyin">{{ $card->pinyin }}</td>
            <td tp_item="tp_translation">{{ $card->translation }}</td>
            <td tp_item="tp_example">{{ $card->example }}</td>
            <td>
                <button class="btn btn-primary" data-toggle="modal" data-target="#tp_modal_card" data-op="edit">Edit</button>
                <button class="btn btn-primary" data-toggle="modal" data-target="#tp_modal_card" data-op="clone">Clone</button>

                <form action="{{ url('/cards', ['id' => $card->id]) }}" method="post">
                    @csrf
                    @method('delete')
                    <button class="btn btn-primary" type="submit">Delete</button>
                </form>
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
        <h4 class="modal-title" id="tp_modal_title">TBD</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">

         <form id="tp_modal_card_form" action="/cards" method="TBD" class="was-validated">

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
            

        } );

        function sendDelete(event){
            console.log($(event.target));
            id = $(event.target).parent().parent().data('id');
            console.log(id);
            $.ajax({
            url: '/cards/' + id,
                type: 'DELETE',
                success: function(result) {
                }
            });
        }

        $('#tp_modal_card').on('show.bs.modal', function (event) {

            // get operation from button that triggered the modal form
            var button = $(event.relatedTarget);
            var op = button.data('op');

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

                // configure modal form
                $('#tp_modal_title').html('Edit Card');
                $('tp_modal_card_form').attr('method', 'PUT');

                // code block
                var el_tr = $(button).parent().parent();
                $('#tp_modal_card #tp_id').val(el_tr.find('[tp_item="tp_id"]').html());
                $('#tp_modal_card #tp_symbol').val(el_tr.find('[tp_item="tp_symbol"]').html());
                $('#tp_modal_card #tp_pinyin').val(el_tr.find('[tp_item="tp_pinyin"]').html());
                $('#tp_modal_card #tp_translation').val(el_tr.find('[tp_item="tp_translation"]').html());

                break;

            case "clone":
                // code block
                var el_tr = $(button).parent().parent();
                $('#tp_modal_card #tp_id').val(el_tr.find('[tp_item="tp_id"]').html());
                $('#tp_modal_card #tp_symbol').val(el_tr.find('[tp_item="tp_symbol"]').html());
                $('#tp_modal_card #tp_pinyin').val(el_tr.find('[tp_item="tp_pinyin"]').html());
                $('#tp_modal_card #tp_translation').val(el_tr.find('[tp_item="tp_translation"]').html());
                break;
            default:
                // code block
                assert(true);
            } 

            var el_tr = $(button).parent().parent();
            $('#tp_modal_card #tp_id').val(el_tr.find('[tp_item="tp_id"]').html());
            $('#tp_modal_card #tp_symbol').val(el_tr.find('[tp_item="tp_symbol"]').html());
            $('#tp_modal_card #tp_pinyin').val(el_tr.find('[tp_item="tp_pinyin"]').html());
            $('#tp_modal_card #tp_translation').val(el_tr.find('[tp_item="tp_translation"]').html());


                var modal = $(this)
                modal.find('.modal-title').text('New message to ' + recipient)
                modal.find('.modal-body input').val(recipient)
        });

        // edit card
        $('.tp_card_edit_button').on('click', function(e) {

            console.log('edit');
            // Prevent default behavior of click event
            e.preventDefault();


            // populate modal form with values of the selected record
            var el_tr = $(this).parent().parent();
            $('#tp_modal_card #tp_id').val(el_tr.find('[tp_item="tp_id"]').html());
            $('#tp_modal_card #tp_symbol').val(el_tr.find('[tp_item="tp_symbol"]').html());
            $('#tp_modal_card #tp_pinyin').val(el_tr.find('[tp_item="tp_pinyin"]').html());
            $('#tp_modal_card #tp_translation').val(el_tr.find('[tp_item="tp_translation"]').html());

            // show modal form
            $('#tp_modal_card').modal('show');

        });

        // clone card
        $('.tp_card_clone_button').on('click', function(e) {

            console.log('clone');
            // Prevent default behavior of click event
            e.preventDefault();

            // configure modal form
            $('#tp_modal_title').html('Clone Card');
            $('tp_modal_card_form').attr('action', '/cards/create');
            $('tp_modal_card_form').attr('method', 'GET');

            // populate modal form with values of the selected record
            var el_tr = $(this).parent().parent();
            $('#tp_modal_card #tp_symbol').val(el_tr.find('[tp_item="tp_symbol"]').html());
            $('#tp_modal_card #tp_pinyin').val(el_tr.find('[tp_item="tp_pinyin"]').html());
            $('#tp_modal_card #tp_translation').val(el_tr.find('[tp_item="tp_translation"]').html());

            // show modal form
            $('#tp_modal_card').modal('show');

        });

        // delete card
        $('.tp_card_delete_button').on('click', function(e) {

            console.log('delete');
            // Prevent default behavior of click event
            e.preventDefault();

            // Send delete request
            /* console.log( $(this).data('id)); */



        });
    </script>
@endpush

