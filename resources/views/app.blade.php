<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<!-- <link rel="stylesheet" href="{{ asset('css/app.css') }}"> -->
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>{{ config('app.name') }}</title>

        <!-- CSS only -->
        <link rel="stylesheet" href="/comp/bootstrap/css/bootstrap.min.css">

        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="/comp/bootstrap-select/css/bootstrap-select.min.css">

        <!-- Add datatables -->
        <link rel="stylesheet" type="text/css" href="/comp/datatables.net/css/jquery.dataTables.css">

        <!-- Add icon library -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">


        @stack('css')

    </head>
	<body>
		@include('navbar')

        <div class="container shadow mb-5 mt-5 pt-2 pb-2 bg-light rounded">
<div class="mb-15">
            <select class="selectpicker" title="No cards selected" multiple data-live-search="true">
                <option>Test</option>
            </select>
            <select class="selectpicker" title="No examples selected" multiple data-live-search="true">
                <option>Test</option>
            </select>
            <select class="selectpicker" title="No labels selected" multiple data-live-search="true">
                <option>Test</option>
            </select>
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

		@yield('content')
		@include('footer')

        <!-- JS, Popper.js, and jQuery -->
        <script src="/comp/jquery/jquery.slim.min.js"></script>
        <script src="/comp/popperjs/umd/popper.min.js"></script>
        <script src="/comp/bootstrap/js/bootstrap.min.js"></script>

        <!-- Latest compiled and minified JavaScript -->
        <script src="/comp/bootstrap-select/js/bootstrap-select.min.js"></script>

        <!-- Datatables -->
        <script type="text/javascript" charset="utf8" src="/comp/datatables.net/js/jquery.dataTables.js"></script>
        <script type="text/javascript" charset="utf8" src="/comp/datatables.net-plugins/api/row().show().js"></script>

        <script>
            $(document).ready( function () {
                $('select').selectpicker();
            });
        </script>
        @stack('scripts')

	</body>
</html>
