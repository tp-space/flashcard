<div class="row mt-3 mb-3">
    <div class="col-md-6 text-md-left d-flex">
        <h1>Quiz</h1><h4 class="align-self-center ml-3">({{-- $countRemain}}/{{ $countAll --}})</h4>
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
    <div class="col-md-6" style="height: 400px">
        <canvas id="myCanvas" style="width: 100%;height: 90%;border: 2px solid black; touch-action: none;"></canvas>
        <button id="clear-canvas" class="btn btn-default" style="width: 100%;height: 10%;">Clear Drawing</button>
    </div>
    <div class="col-md-6" style="background-color:orange;">
        <div>
            <button 
                id="fc-card-tts" data-state="{{ Session::get('fc-card-tts', 'off') }}" 
                class="btn btn-sm fc-toggle-icon">
                <i class="fa"></i>
            </button>
            @php ($audioPath = App\Http\Controllers\AudioController::getAudioFilePath(App\Http\Controllers\AudioController::CARD, $card->id))
            @if (file_exists($audioPath['fs']))
                <button 
                    id="fc-card-tts-item" 
                    class="btn btn-sm btn-primary fc-audio" 
                    style="display: none;" 
                    data-path="{{ $audioPath['url'] }}">
                    <i class="fa fa-play"></i>
                </button>
            @else
            <span>No audio file available</span>
            @endif
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

<div id="fc-examples-item">
    <table id="tp_quiz_table" class="display" style="width:100%;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Example</th>
                <th>Translation</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

@push ('scripts')
    <script>

        var mousePressed = false;
        var touchPressed = false;
        var lastMouseX, lastMouseY;
        var lastTouchX, lastTouchY;
        var ctx;

        $(document).ready( function () {


            initCanvas();

            refresh_all(true);

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

        $(document).on('click', '#clear-canvas', function (event) {
            // Use the identity matrix while clearing the canvas
            ctx.setTransform(1, 0, 0, 1, 0, 0);
            ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
        });

        function refresh_all(onLoad = false){

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

                // playback
                if (onLoad && (state == 'on') && (elem[i] == 'fc-card-tts')){
                    $(".fc-audio").trigger('click');
                }
            }
        }

        function initCanvas() {

            var canvas = document.querySelector('#myCanvas');
            ctx = canvas.getContext("2d");

            // prevent canvas stretching by setting the canvas size to the element's size
            canvas.width = canvas.offsetWidth;
            canvas.height = canvas.offsetHeight;

            // add touch event handler (for tablets)
            canvas.addEventListener("touchstart", function (e) {
                touchPressed = true;
                var touch = e.touches[0];
                x = touch.pageX - $(this).offset().left;
                y = touch.pageY - $(this).offset().top;
                drawTouchCanvas(x, y, false);
            });

            canvas.addEventListener("touchmove", function (e) {
                if (touchPressed) {
                    var touch = e.touches[0];
                    x = touch.pageX - $(this).offset().left;
                    y = touch.pageY - $(this).offset().top;
                    drawTouchCanvas(x, y, true);
                }
            });

            canvas.addEventListener("touchend", function (e) {
                touchPressed = false;
            });

            canvas.addEventListener("touchcancel", function (e) {
                touchPressed = false;
            });


            // add mouse event handler
            $('#myCanvas').mousedown(function (e) {
                mousePressed = true;
                x = e.pageX - $(this).offset().left;
                y = e.pageY - $(this).offset().top;
                drawMouseCanvas(x, y, false);
            });

            $('#myCanvas').mousemove(function (e) {
                if (mousePressed) {
                    x = e.pageX - $(this).offset().left;
                    y = e.pageY - $(this).offset().top;
                    drawMouseCanvas(x, y, true);
                }
            });

            $('#myCanvas').mouseup(function (e) {
                mousePressed = false;
            });

            $('#myCanvas').mouseleave(function (e) {
                mousePressed = false;
            });
        }

        function drawTouchCanvas(x, y, isDown) {
            if (isDown) {
                ctx.beginPath();
                ctx.strokeStyle = 'black';
                ctx.lineWidth = 1;
                ctx.lineJoin = "round";
                ctx.moveTo(lastTouchX, lastTouchY);
                ctx.lineTo(x, y);
                ctx.closePath();
                ctx.stroke();
            }
            lastTouchX = x; lastTouchY = y;
        }

        function drawMouseCanvas(x, y, isDown) {
            if (isDown) {
                ctx.beginPath();
                ctx.strokeStyle = 'black';
                ctx.lineWidth = 1;
                ctx.lineJoin = "round";
                ctx.moveTo(lastMouseX, lastMouseY);
                ctx.lineTo(x, y);
                ctx.closePath();
                ctx.stroke();
            }
            lastMouseX = x; lastMouseY = y;
        }
    </script>
@endpush
