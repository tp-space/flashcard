@extends('app')

@push ('css')
@endpush

@section('content')
<div id="tp_content_loading" class="container shadow mb-5 mt-5 bg-light rounded">
    loading
</div>
<div id="tp_content" class="container shadow mb-5 mt-5 bg-light rounded" style="display: none;">

    <div class="row mt-3 mb-3">
        <div class="col-md-6 text-md-left d-flex">
            <h1>Quiz</h1><h4 class="align-self-center ml-3">({{ $countRemain}}/{{ $countAll }})</h4>
        </div>
        <div class="col-md-6 text-md-right">
            <a class="btn btn-danger" title="Put all cards back into the stack" href="/quiz/reset">Reset</a>
            @if (isset($card) && $card != null)
            <button id="fc-show-hide" title="Show card info" class="btn btn-primary fc-show-hide">Show</button>
            <a class="btn btn-warning" title="Keep card in stack" href="/quiz">Keep</a>
            <a class="btn btn-success" title="Remove card from stack" href="/quiz/done/{{ $card->id }}">Next</a>
            @endif
        </div>
    </div>

    @if (!isset($card) || $card == null)
        <span>No cards left</span>
    @else
    <div class="row mt-3 mb-3">
        <div class="col-md-12 text-md-left">
            <button 
                id="fc-labels" data-state="{{ Session::get('fc-labels', 'off') }}" 
                class="btn btn-sm fc-toggle-icon">
                <i class="fa"></i>
            </button>
            <span id="fc-labels-item" style="display: none;">
                {{ implode(", ", $card->labels()->pluck('label')->toArray()) }}
            </span>
        </div>
    </div>

    <div class="row mt-3 mb-3">
        <div class="col-md-6" style="height: 400px; background-color:beige;">
        </div>
        <div class="col-md-6" style="background-color:orange;">
            <div>
                <button 
                    id="fc-card-tts" data-state="{{ Session::get('fc-card-tts', 'off') }}" 
                    class="btn btn-sm fc-toggle-icon">
                    <i class="fa"></i>
                </button>
                <i id="fc-card-tts-item" style="display: none;" class="fa fa-volume-up"></i>
            </div>
            <div>
                <button 
                    id="fc-symbol" data-state="{{ Session::get('fc-symbol', 'off') }}" 
                    class="btn btn-sm fc-toggle-icon">
                    <i class="fa"></i>
                </button>
                <span id="fc-symbol-item" class="h1" style="display: none;">{{ $card->symbol }}</span>
            </div>
            <div>
                <button 
                    id="fc-pinyin" data-state="{{ Session::get('fc-pinyin', 'off') }}" 
                    class="btn btn-sm fc-toggle-icon">
                    <i class="fa"></i>
                </button>
                <span id="fc-pinyin-item" style="display: none;">
                    {{ $card->pinyin }}
                </span>
            </div>
            <div>
                <button 
                    id="fc-translation" data-state="{{ Session::get('fc-translation', 'off') }}"
                    class="btn btn-sm fc-toggle-icon">
                    <i class="fa"></i>
                </button>
                <span id="fc-translation-item" style="display: none;">
                    {{ $card->translation }}
                </span>
            </div>
            <div>
                <button 
                    id="fc-comment" data-state="{{ Session::get('fc-comment', 'off') }}" 
                    class="btn btn-sm fc-toggle-icon">
                    <i class="fa"></i>
                </button>
                <span id="fc-comment-item" style="display: none;">
                    {{ $card->comment }}
                </span>
            </div>
        </div>
    </div>
    @endif

    @if (isset($card) && $card != null)
    <button 
       id="fc-examples" data-state="{{ Session::get('fc-examples', 'off') }}" 
       class="btn btn-sm fc-toggle-icon">
        <i class="fa"></i>
    </button>
    @endif

    <div id="fc-examples-item" style="display: none;">
        <table id="tp_quiz_table" class="display" style="width:100%;">
            <thead>
                <tr>
                    <th>Example</th>
                    <th>Translation</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
            @if (isset($card) && $card != null)
                @foreach ($card->examples as $example)
                <tr>
                    <td>{{ $example->example }}</td>
                    <td >{{ $example->translation }}</td>
                    <td class="text-center">
                        <button class="btn btn-sm">
                            <i class="fa fa-volume-up"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            @endif
            </tbody>
        </table>
    </div>

</div>
@endsection

@push ('scripts')
    <script>
        $(document).ready( function () {

            var table = $('#tp_quiz_table').DataTable({
                paging: false,
                searching: false,
                initComplete: function(settings, json, example_id) {
                    $('#tp_content_loading').hide();
                    $('#tp_content').show();
                }
            });

            refresh_all();

        });

        $(document).on('click', '.fc-show-hide', function (event) {

            if ($(this).text() == 'Show'){
                $(this).text('Hide');
                $(this).attr('title', 'Hide card information');
            } else {
                $(this).text('Show');
                $(this).attr('title', 'Show card information');
            }

            refresh_all();

        });

        $(document).on('click', '.fc-toggle-icon', function (event) {

            newState = ($(this).data('state') == 'on' ? 'off' : 'on');
            $(this).data('state', newState);

            $.ajax({
            type: 'POST',
                url: '/quiz/update_state',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: { 
                    key: $(this).attr('id'), 
                    state: newState
                } ,
                success: function(data, status){console.log(data,status)},
            })

            refresh_all();

        });

        function refresh_all(){

            elem = [
                'fc-labels', 
                'fc-card-tts', 
                'fc-symbol', 
                'fc-pinyin', 
                'fc-translation', 
                'fc-comment', 
                'fc-examples'
            ];
            
            fcIsShow = ($('#fc-show-hide').text() == 'Hide');

            for (let i = 0; i < elem.length; i++){

                state = $('#' + elem[i]).data('state');
                btn = $('#' + elem[i]);
                icon = btn.find('i');
                span = $('#' + elem[i] + '-item');

                // set correct icon and button color
                if (state == 'on'){
                    if (!icon.hasClass('fa-eye-slash')){
                        icon.removeClass('fa-eye');
                        icon.addClass('fa-eye-slash');
                    }
                    if (!btn.hasClass('btn-success')){
                        btn.removeClass('btn-warning');
                        btn.addClass('btn-success');
                    }
                } else {
                    if (!icon.hasClass('fa-eye-eye')){
                        icon.removeClass('fa-eye-slash');
                        icon.addClass('fa-eye');
                    }
                    if (!btn.hasClass('btn-warning')){
                        btn.removeClass('btn-success');
                        btn.addClass('btn-warning');
                    }
                }

                // show hide item
                if (fcIsShow || (state == 'on')){
                    span.show();
                } else {
                    span.hide();
                }
            }

        }

    </script>
@endpush
