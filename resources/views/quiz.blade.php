@extends('app')

@push ('css')
@endpush

@section('content')
<div id="tp_content_loading" class="container shadow mb-5 mt-5 bg-light rounded">
    loading
</div>
<div id="tp_content" class="container shadow mb-5 mt-5 bg-light rounded" style="display: none;">

    <div class="row mt-3 mb-3">
        <div class="col-md-6 text-md-left">
            <h1>Quiz</h1>
        </div>
        <div class="col-md-6 text-md-right">
            <button class="btn btn-danger">Reset</button>
            <button class="btn btn-primary">Show/Hide</button>
            <button class="btn btn-warning">Keep</button>
            <button class="btn btn-success">Next</button>
        </div>
    </div>

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

    <table id="tp_quiz_table" class="display" style="width:100%;">
        <thead>
            <tr>
                <th>Example</th>
                <th>Translation</th>
                <th class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($card->examples as $example)
            <tr>
                <td>{{ $example->example }}</td>
                <td >{{ $example->translation }}</td>
                <td class="text-center">
                    <button class="btn btn-sm">
                        <i class="fa fa-edit"></i>
                    </button>
                </td>
            </tr>
            @endforeach
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
    </script>
@endpush
