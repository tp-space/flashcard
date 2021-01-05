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
                            class="form-control tp_filter" 
                            id="tp_labels" 
                            name="tp_labels[]" 
                            data-type="label" 
                            data-placeholder="No labels selected" 
                            style="display: none;width: 100%;"
                            multiple>
                        </select>
                        <div class="invalid-feedback">Please select at least one entry.</div>
                    </div>

                    <div class="form-group">
                        <label for="tp_examples">Examples:</label>
                        <select 
                            class="form-control tp_filter" 
                            id="tp_examples" 
                            name="tp_examples[]" 
                            data-type="example" 
                            data-placeholder="No examples selected" 
                            style="display: none;width: 100%;"
                            multiple>
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
