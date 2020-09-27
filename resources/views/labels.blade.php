@extends('app')

@push ('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.css">
@endpush

@section ('content')
    <div class="container">

        <h1>Labels</h1>

        <table id="tp_card" class="display">
        <thead>
            <tr>
                <th>Label name</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($labels as $label)
                <tr>
                    <td>{{ $label->name }}</td>
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

