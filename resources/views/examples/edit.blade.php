<div class="modal" id="tp_modal_example">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title" id="tp_modal_example_title">TBD</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">

                <form id="tp_modal_example_form" class="was-validated">

                    <input type="hidden" id="tp_example_id" name="tp_example_id">
                    <input type="hidden" id="tp_example_user_id" name="tp_example_user_id">

                    <div class="form-group">
                        <label for="tp_example_example">Example:</label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="tp_example_example" 
                            name="tp_example_example" 
                            placeholder="Enter example" 
                            required>
                        <div class="invalid-feedback">Please fill out this field.</div>
                    </div>

                    <div class="form-group">
                        <label for="tp_example_translation">Translation:</label>
                        <input 
                               type="text" 
                               class="form-control" 
                               id="tp_example_translation" 
                               name="tp_example_translation" 
                               placeholder="Enter translation" 
                               required>
                        <div class="invalid-feedback">Please fill out this field.</div>
                    </div>

                    <div class="form-group">
                        <label for="tp_example_cards">Cards:</label>
                        <select 
                            class="form-control tp_filter" 
                            id="tp_example_cards" 
                            name="tp_example_cards[]" 
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
                <button type="button" data-dismiss="modal" id="tp_example_submit" class="btn btn-primary">Submit</button>
                <button type="button" data-dismiss="modal" class="btn btn-standard">Close</button>
            </div>

        </div>
    </div>
</div>
