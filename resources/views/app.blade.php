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

        <!-- Add datatables -->
        <link rel="stylesheet" type="text/css" href="/comp/datatables.net/css/jquery.dataTables.css">

        <!-- Add icon library -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">


        @stack('css')

    </head>
	<body>

		@include('navbar')

        @if (isset($filterLabels) && isset($filterCards) && isset($filterExamples) && isset($filterUsers))
        <div class="container shadow mb-5 mt-5 pt-2 pb-2 bg-light rounded">
            <div class="mb-15">
                <div class="container">
                    <form id="tp_filter_form" action="/filter" method="POST">
                        <div class="row">

                            @php ($sel_labels = Session::get('filter_label_ids', []))
                            @php ($sel_cards = Session::get('filter_card_ids', []))
                            @php ($sel_examples = Session::get('filter_example_ids', []))
                            @php ($sel_users = Session::get('filter_user_ids', 0))
                            @php ($hasFilter = (count($sel_cards) + count($sel_labels) + count($sel_examples) > 0))

                            @csrf
                            <input name="tp_url" type="hidden" value="{{ Request::url() }}">

                            <button 
                                type="submit" 
                                name="tp_filter_clear"
                                title="Clear Filter" 
                                class="btn btn-primary"
                                {{ $hasFilter ? '' : 'disabled="disabled"' }}>
                                <i class="fa fa-filter"></i>
                            </button>

                            <select 
                               id="tp_filter_label" 
                               name="tp_filter_label[]" 
                               class="selectpicker filter" 
                               title="No labels selected" 
                               onchange="this.form.submit()" 
                               multiple data-live-search="true">

                                @foreach ($filterLabels as $filterLabel)
                                @php ($sel = (in_array($filterLabel->id, $sel_labels) ? 'selected' : ''))
                                <option {{ $sel }} value="{{ $filterLabel->id }}">
                                    {{ $filterLabel->label }}
                                </option>
                                @endforeach
                            </select>

                            <select 
                               id="tp_filter_card" 
                               name="tp_filter_card[]" 
                               class="selectpicker filter" 
                               title="No cards selected" 
                               onchange="this.form.submit()" 
                               multiple data-live-search="true">

                                @foreach ($filterCards as $filterCard)
                                @php ($sel = (in_array($filterCard->id, $sel_cards) ? 'selected' : ''))
                                    <option {{ $sel }} value="{{ $filterCard->id }}">
                                        {{ $filterCard->symbol }}
                                    </option>
                                @endforeach

                            </select>

                            <select 
                               id="tp_filter_example" 
                               name="tp_filter_example[]" 
                               class="selectpicker filter" 
                               title="No examples selected" 
                               onchange="this.form.submit()" 
                               multiple data-live-search="true">

                                @foreach ($filterExamples as $filterExample)
                                @php ($sel = (in_array($filterExample->id, $sel_examples) ? 'selected' : ''))
                                <option {{ $sel }} value="{{ $filterExample->id }}">
                                    {{ $filterExample->example }}
                                </option>
                                @endforeach

                            </select>

                            {{-- Separator --}}
                            <div style="margin-left:auto"></div>

                            <select 
                                id="tp_filter_user" 
                                name="tp_filter_user" 
                                class="selectpicker filter text-right" 
                                style="margin-left:auto"
                                data-width="fit"
                                title="No user selected" onchange="this.form.submit()">

                                @foreach ($filterUsers as $user)
                                @php ($sel = ($user->id == $sel_users ? 'selected' : ''))
                                <option {{ $sel }} value="{{ $user->id }}">
                                    {{ $user->name }}
                                </option>
                                @endforeach

                            </select>


                        </div>
                    </form>
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
        @endif

		@yield('content')
		@include('footer')

        <!-- JS, Popper.js, and jQuery -->
        <script src="/comp/jquery/jquery.min.js"></script>
        <script src="/comp/popperjs/umd/popper.min.js"></script>
        <script src="/comp/bootstrap/js/bootstrap.min.js"></script>

        <!-- Latest compiled and minified JavaScript -->
        <script src="/comp/bootstrap-select/js/bootstrap-select.min.js"></script>

        <!-- Datatables -->
        <script type="text/javascript" charset="utf8" src="/comp/datatables.net/js/jquery.dataTables.js"></script>
        <script type="text/javascript" charset="utf8" src="/comp/datatables.net-plugins/api/row().show().js"></script>

        <script>
            $(document).ready( function () {
                $('.filter').selectpicker();
            });

        $(document).on('click', '.fc-audio', function (event) {

            var src = $(this).data('path');
            var audio = new Audio(src);
            audio.load();
            audio.play();
            return;

        });

        </script>
        @stack('scripts')

	</body>
</html>
