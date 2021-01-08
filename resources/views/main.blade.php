@extends ('app')

@push ('css')
@endpush

@section ('content')

    <div class="container shadow mb-5 mt-5 pt-2 pb-2 bg-light rounded">
        <div class="mb-15">
            <div class="container">
                <div class="row">

                    <button 
                        id="tp_filter_clear"
                        title="Clear Filter" 
                        class="btn btn-primary">
                        <i class="fa fa-filter"></i>
                    </button>

                    <select 
                       id="tp_filter_label" 
                       name="tp_filter_label[]" 
                       class="tp_filter" 
                       data-type="label"
                       data-placeholder="No labels selected"
                       style="display: none;width: 20%;"
                       multiple>
                    </select>

                    <select 
                       id="tp_filter_card" 
                       name="tp_filter_card[]" 
                       class="tp_filter" 
                       data-type="card"
                       data-placeholder="No cards selected" 
                       style="display: none;width: 20%;"
                       multiple>
                    </select>

                    <select 
                       id="tp_filter_example" 
                       name="tp_filter_example[]" 
                       class="tp_filter" 
                       data-type="example"
                       data-placeholder="No examples selected" 
                       style="display: none;width: 20%;"
                       multiple>
                    </select>

                    {{-- Separator --}}
                    <div style="margin-left:auto"></div>

                    <select 
                        id="tp_user" 
                        name="tp_user" 
                        class="tp_user text-right" 
                        style="margin-left:auto"
                        data-width="fit"
                        title="No user selected">
                    </select>

                </div>

                <div class="row">
                    <div class="col-12">
                        @if ($message = Session::get('success'))
                        <div class="alert alert-success alert-block">
                            <button type="button" class="close" data-dismiss="alert">x</button>
                            <span>{{ $message }}</span>
                        </div>
                        @endif
                        @if ($message = Session::get('error'))
                        <div class="alert alert-danger alert-block">
                            <button type="button" class="close" data-dismiss="alert">x</button>
                            <span>{{ $message }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div id="tp_label" class="container shadow mb-5 mt-5 bg-light rounded" style="display: none;">
        @include ('labels')
    </div>
    <div id="tp_card" class="container shadow mb-5 mt-5 bg-light rounded" style="display: none;">
        @include ('cards')
    </div>
    <div id="tp_example" class="container shadow mb-5 mt-5 bg-light rounded" style="display: none;">
        @include ('examples')
    </div>
    <div id="tp_quiz" class="container shadow mb-5 mt-5 bg-light rounded" style="display: none;">
        @include ('quiz')
    </div>

    @include ('labels.edit')
    @include ('labels.delete')

    @include ('cards.edit')
    @include ('cards.delete')

    @include ('examples.edit')
    @include ('examples.delete')

@endsection

@push ('scripts')
    <script src="{{ asset('js/main.js') }}"></script>
@endpush

