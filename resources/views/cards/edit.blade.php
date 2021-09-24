<!-- The Modal Create/Edit/clone -->
<div class="modal" id="tp_modal_card">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title" id="tp_modal_card_title"></h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">

                <form id="tp_modal_card_form">

                    <input type="hidden" id="tp_card_id" name="tp_card_id">
                    <input type="hidden" id="tp_card_user_id" name="tp_card_user_id">

                    <div class="form-group">
                        <label for="tp_card_symbol">Symbol:</label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="tp_card_symbol" 
                            placeholder="Enter chinese character" 
                            name="tp_card_symbol" 
                            required>
                        <div class="invalid-feedback">Please fill out this field.</div>
                    </div>

                    <div class="form-group">
                        <label for="tp_card_pinyin">PinYin:</label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="tp_card_pinyin" 
                            placeholder="Enter pinyin" 
                            name="tp_card_pinyin" 
                            required>
                        <div class="invalid-feedback">Please fill out this field.</div>
                    </div>

                    <div class="form-group">
                        <label for="tp_card_translation">Translation:</label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="tp_card_translation" 
                            placeholder="Enter the translation" 
                            name="tp_card_translation" 
                            required>
                        <div class="invalid-feedback">Please fill out this field.</div>
                    </div>

                    <div class="form-group">
                        <label for="tp_card_comment">Comment:</label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="tp_card_comment" 
                            placeholder="Enter the comment" 
                            name="tp_card_comment">
                    </div>

                    <div class="form-group">
                        <label for="tp_card_labels">Labels:</label>
                        <select 
                            class="form-control tp_filter" 
                            id="tp_card_labels" 
                            name="tp_card_labels[]" 
                            data-type="label" 
                            data-placeholder="No labels selected" 
                            style="display: none;width: 100%;"
                            multiple>
                        </select>
                        <div class="invalid-feedback">Please select at least one entry.</div>
                    </div>

                    <div class="form-group">
                        <label for="tp_card_examples">Examples:</label>
                        <select 
                            class="form-control tp_filter" 
                            id="tp_card_examples" 
                            name="tp_card_examples[]" 
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
                <button type="button" data-dismiss="modal" class="btn btn-primary" id="tp_card_submit">Submit</button>
                <button type="button" data-dismiss="modal" class="btn btn-standard" >Close</button>
            </div>

        </div>
    </div>
</div>
