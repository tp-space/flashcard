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
            <button title="Hide card info" class="btn btn-primary fc_show_hide">Hide</button>
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
            <span>{{ implode(", ", $card->labels()->pluck('label')->toArray()) }}</span>
        </div>
    </div>

    <div class="row mt-3 mb-3">
        <div class="col-md-6" style="height: 400px; background-color:beige;">
        </div>
        <div class="col-md-6" style="background-color:orange;">
            <div>
                <i class="fa fa-volume-up"></i>
            </div>
            <div>
                <h1>{{ $card->symbol }}</h1>
            </div>
            <div>
                <span>{{ $card->pinyin }}</span>
            </div>
            <div>
                <span>{{ $card->translation }}</span>
            </div>
            <div>
                <span>{{ $card->comment }}</span>
            </div>
        </div>
    </div>
    @endif

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

        });

        $(document).on('click', '.fc_show_hide', function (event) {

            if ($(this).text() == 'Show'){
                $(this).text('Hide');
                $(this).attr('title', 'Hide card information');
            } else {
                $(this).text('Show');
                $(this).attr('title', 'Show card information');
            }

        });
    </script>
@endpush
