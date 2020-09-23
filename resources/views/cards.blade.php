@extends('app')

@push ('css')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.css">

@endpush

@section ('content')
<div class="container">
        <h1>This is the cards content</h1>

<table id="tp_card" class="display">
<thead>
    <tr>
        <th>Symbol</th>
        <th>Pinyin</th>
        <th>Translation</th>
        <th>Examples</th>
    </tr>
</thead>
<tbody>
        @foreach ($cards as $card)
    <tr>
        <td>{{ $card->symbol }}</td>
        <td>{{ $card->pinyin }}</td>
        <td>{{ $card->translate }}</td>
        <td>{{ $card->example }}</td>
    </tr>
        @endforeach
        </tbody>
</table>

</div>
@endsection

@push ('scripts')

<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.js"></script>

<script>
$(document).ready( function () {
    $('#tp_card').DataTable();
} );
</script>

@endpush

