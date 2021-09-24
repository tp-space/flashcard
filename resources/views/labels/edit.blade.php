<div class="modal" id="tp_modal_label">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title" id="tp_modal_label_title">TBD</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">

                <form id="tp_modal_label_form" class="was-validated">

                    <input type="hidden" id="tp_label_id" name="tp_label_id">
                    <input type="hidden" id="tp_label_user_id" name="tp_label_user_id">

                    <div class="form-group">
                        <label for="tp_label_label">Label:</label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="tp_label_label" 
                            name="tp_label_label" 
                            placeholder="Enter label" 
                            required>
                        <div class="invalid-feedback">Please fill out this field.</div>
                    </div>

                    <div class="form-group">
                        <label for="tp_label_cards">Cards:</label>
                        <select 
                            class="form-control tp_filter" 
                            id="tp_label_cards" 
                            name="tp_label_cards[]" 
                            data-placeholder="No cards selected" 
                            style="display: none;width: 100%;"
                            data-type="card" 
                            multiple>
                        </select>
                        <div class="invalid-feedback">Please select at least one entry.</div>
                    </div>

                </form>

            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" id="tp_label_submit" class="btn btn-primary">Submit</button>
                <button type="button" data-dismiss="modal" class="btn btn-standard">Close</button>
            </div>

        </div>
    </div>
</div>
