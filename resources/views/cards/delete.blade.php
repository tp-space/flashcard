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

