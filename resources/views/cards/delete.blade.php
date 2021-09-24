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
                <form id="tp_card_delete_form">
                    <input type="hidden" id="tp_card_delete_id" name="tp_card_delete_id">
                    <input type="hidden" id="tp_card_delete_user_id" name="tp_card_delete_user_id">
                    <p id="tp_card_delete_text"></p>
                </form>
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" id="tp_card_delete" class="btn btn-danger">Delete</button>
                <button type="button" data-dismiss="modal" class="btn btn-standard">Close</button>
            </div>

        </div>
    </div>
</div>

