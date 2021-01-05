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
                            class="form-control tp_filter" 
                            id="tp_cards" 
                            name="tp_cards[]" 
                            data-placeholder="No cards selected" 
                            data-type="card" 
                            style="display: none;width: 100%;"
                            multiple>
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
