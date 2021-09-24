<div class="row mt-3 mb-3">
    <div class="col-md-6 text-md-left d-flex">
        <h1>Quiz</h1>
        <h4 id="tp-stats" class="align-self-center ml-3">(no stats available)</h4>
    </div>
    <div class="col-md-6 text-md-right">
        <button id="tp-reset" class="btn btn-danger" title="Put all cards back into the stack">Reset</button>
        <button id="tp-show-hide" class="btn btn-primary tp-has-card" title="Show card info">Show</button>
        <button id="tp-keep" class="btn btn-warning tp-has-card" title="Keep card in stack">Keep</button>
        <button id="tp-next" class="btn btn-success tp-has-card" title="Remove card from stack">Next</button>
    </div>
</div>

<span class="tp-no-card">No cards left</span>

<div class="tp-has-card">
    <div class="row mt-3 mb-3">
        <div class="col-md-6">

            <div>

                <div class="row">
                    <button id="tp-toggle-label" class="btn btn-sm btn-link tp-toggle col-sm-3 text-left">Labels:</button>
                    <div class="col-sm-9">
                        <span class="tp-toggle-label"></span>
                    </div>
                </div>

            </div>
            <div>

                <div class="row">
                    <button id="tp-toggle-audio" class="btn btn-sm btn-link tp-toggle col-sm-3 text-left">Sound:</button>
                    <div class="col-sm-9">
                        <span class="tp-toggle-audio">
                            <button id="tp-card-audio" class="btn btn-sm btn-primary tp-audio tp-has-audio" data-path="">
                                <i class="fa fa-play"></i>
                            </button>
                            <span class="tp-no-audio">No audio file available</span>
                        </span>
                    </div>
                </div>

            </div>
            <div>

                <div class="row">
                    <button id="tp-toggle-symbol" class="btn btn-sm btn-link tp-toggle col-sm-3 text-left">Symbol:</button>
                    <div class="col-sm-9">
                        <strong><span class="h2 tp-toggle-symbol"></span></strong>
                    </div>
                </div>

            </div>
            <div>

                <div class="row">
                    <button id="tp-toggle-pinyin" class="btn btn-sm btn-link tp-toggle col-sm-3 text-left">Pinyin:</button>
                    <div class="col-sm-9">
                        <span class="tp-toggle-pinyin"></span>
                    </div>
                </div>

            </div>
            <div>

                <div class="row">
                    <button id="tp-toggle-translation" class="btn btn-sm btn-link tp-toggle col-sm-3 text-left">Translation:</button>
                    <div class="col-sm-9">
                        <span class="tp-toggle-translation"></span>
                    </div>
                </div>

            </div>
            <div>

                <div class="row">
                    <button id="tp-toggle-comment" class="btn btn-sm btn-link tp-toggle col-sm-12 text-left">Comment:</button>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <span class="tp-toggle-comment"></span>
                    </div>
                </div>

            </div>
        </div>
        <div class="col-md-6" style="height: 300px">
            <button id="tp-canvas-clear" class="btn btn-secondary" style="width: 100%;height: 10%;">Clear Drawing</button>
            <canvas id="tp-canvas" style="width: 100%;height: 90%;border: 2px solid black; touch-action: none;"></canvas>
        </div>
    </div>

    <button id="tp-toggle-example" class="btn btn-sm btn-link tp-toggle">Examples:</button>
    <div class="tp-toggle-example">
        <table id="tp_quiz_table" class="display" style="width:100%;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Example</th>
                    <th>Translation</th>
                </tr>
            </thead>
        </table>
    </div>

</div>

