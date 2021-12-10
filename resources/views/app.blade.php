<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<!-- <link rel="stylesheet" href="{{ asset('css/app.css') }}"> -->
		<meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}"/>

		<title>{{ config('app.name') }}</title>

        <!-- CSS only -->
        <link rel="stylesheet" href="/comp/bootstrap/css/bootstrap.min.css">

        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="/comp/bootstrap-select/css/bootstrap-select.min.css">

        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="/comp/select2/css/select2.min.css">

        <!-- Add datatables -->
        <link rel="stylesheet" type="text/css" href="/comp/datatables.net/css/jquery.dataTables.css">

        <!-- Add icon library -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">


        @stack('css')

    </head>
	<body>

		@include('navbar')

		@yield('content')

		@include('footer')

        <!-- JS, Popper.js, and jQuery -->
        <script src="/comp/jquery/jquery.min.js"></script>
        <script src="/comp/popperjs/umd/popper.min.js"></script>
        <script src="/comp/bootstrap/js/bootstrap.min.js"></script>

        <!-- Latest compiled and minified JavaScript -->
        <script src="/comp/bootstrap-select/js/bootstrap-select.min.js"></script>

        <!-- Latest compiled and minified JavaScript bootstrap-select -->
        <script src="/comp/select2/js/select2.min.js"></script>

        <!-- Latest compiled and minified hanzi-writer JavaScript -->
        <script src="/comp/hanzi-writer/hanzi-writer.min.js"></script>

        <!-- Datatables -->
        <script type="text/javascript" charset="utf8" src="/comp/datatables.net/js/jquery.dataTables.js"></script>
        <script type="text/javascript" charset="utf8" src="/comp/datatables.net-plugins/api/row().show().js"></script>

        @stack('scripts')

	</body>
</html>
