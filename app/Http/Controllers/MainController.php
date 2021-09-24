<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Label;
use App\Models\Card;
use App\Models\Example;
use Illuminate\Http\Request;
use Response;

class MainController extends Controller
{

    const MAIN_NONE = "";
    const MAIN_LABEL = "label";
    const MAIN_CARD = "card";
    const MAIN_EXAMPLE = "example";
    const MAIN_QUIZ = "quiz";
    const MAIN_STATS = "stats";

    public static function appList(){
        return [
            self::MAIN_LABEL,
            self::MAIN_CARD,
            self::MAIN_EXAMPLE,
            self::MAIN_QUIZ,
            self::MAIN_STATS,
        ];
    }

    public static function userList(){
        return User::pluck('id')->toArray();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $userData = [[
            'id' => Auth::id(),
            'text' => Auth::user()->name,
        ]];
        return view('main', compact('userData'));
    }

    public function store_labels(Request $request){

        $id = 0;
        $msg = "";

        do {

            // get user id
            $user = $request->get("tp_label_user_id", null);
            if ($user){
                $userId = json_decode($user)[0]->id;
            } else {
                $msg = "Unknown user id";
                break;
            }

            $id = $request->get("tp_label_id", 0);
            if ($id > 0){
                // update existing label
                $label = Label::where('id', $id)
                    ->where('user_id', $userId)
                    ->first();
                if ($label == null){
                    $msg = "Unable to find label " . $id;
                    break;
                }
                $opType = " has been updated";
            } else {
                // add new label
                $label = new Label;
                $opType = " has been added";
            }

            // populate values
            $label->label = $request->get("tp_label_label","<unknown>");
            $label->user_id = $userId;
            $label->save();

            // update relationship
            $label->cards()->sync($request->get("tp_label_cards", "[]"));

            // generate data to be returned
            $msg = "Label " . $label->label . " has been " . $opType;
            $id = $label->id;

        } while(0);

        return Response::json([
            "id" => $id,
            "message" => $msg,
        ]);
    }

    public function delete_labels(Request $request){

        $id = 0;
        $msg = "";

        do {

            // get user id
            $user = $request->get("tp_label_delete_user_id", null);
            if ($user){
                $userId = json_decode($user)[0]->id;
            } else {
                $msg = "Unknown user id";
                break;
            }

            // get label id
            $id = $request->get("tp_label_delete_id", 0);
            if ($id < 1){
                $msg = "Label id is missing";
                break;
            }

            // search label
            $label = Label::where('id', $id)->where('user_id', $userId)->first();
            if ($label == null){
                $msg = "Unable to find label id " . $id;
                $id = 0;
                break;
            }

            // remove relationship and delete label
            $label->cards()->detach();
            $label->delete();

            // create return message
            $msg = "Label " . $label->label . " has been deleted";

        } while (0);

        return Response::json([
            "id" => $id,
            "message" => $msg,
        ]);
    }

    public function store_cards(Request $request){

        $id = 0;
        $msg = "";

        do {

            // get user id
            $user = $request->get("tp_card_user_id", null);
            if ($user){
                $userId = json_decode($user)[0]->id;
            } else {
                $msg = "Unknown user id";
                break;
            }

            $id = $request->get("tp_card_id", 0);
            if ($id > 0){
                // update existing label
                $card = Card::where('id', $id)
                    ->where('user_id', $userId)
                    ->first();
                if ($card == null){
                    $msg = "Unable to find card " . $id;
                    break;
                }
                $done = $card->done;
                $opType = " has been updated";
            } else {
                // add new label
                $card = new Card;
                $opType = " has been added";
                $done = false;
            }

            // populate values
            $card->user_id = $userId;
            $card->symbol = $request->get("tp_card_symbol","<unknown>");
            $card->pinyin = $request->get("tp_card_pinyin","<unknown>");
            $card->translation = $request->get("tp_card_translation","<unknown>");
            $card->comment = $request->input("tp_card_comment","<unknown>");
            $card->comment = (is_null($card->comment) ? "" : $card->comment);
            $card->done = $done;
            $card->save();

            // update relationship
            $card->labels()->sync($request->get("tp_card_labels", []));
            $card->examples()->sync($request->get("tp_card_examples", []));

            // generate audio file
            AudioController::generateAudioFile(AudioController::CARD, $card->id, $card->symbol);

            // generate data to be returned
            $msg = "Card " . $card->symbol . " has been " . $opType;
            $id = $card->id;

        } while(0);

        return Response::json([
            "id" => $id,
            "message" => $msg,
        ]);
    }

    public function delete_cards(Request $request){

        $id = 0;
        $msg = "";

        do {

            // get user id
            $user = $request->get("tp_card_delete_user_id", null);
            if ($user){
                $userId = json_decode($user)[0]->id;
            } else {
                $msg = "Unknown user id";
                break;
            }

            // get label id
            $id = $request->get("tp_card_delete_id", 0);
            if ($id < 1){
                $msg = "Card id is missing";
                break;
            }

            // search card
            $card = Card::where('id', $id)->where('user_id', $userId)->first();
            if ($card == null){
                $msg = "Unable to find card id " . $id;
                $id = 0;
                break;
            }

            // remove relationship and delete label
            $card->labels()->detach();
            $card->examples()->detach();
            $card->delete();

            // Remove audio file
            AudioController::deleteAudioFile(AudioController::CARD, $card->id);

            // create return message
            $msg = 'Card "' . $card->symbol . '" has been deleted';

        } while (0);

        return Response::json([
            "id" => $id,
            "message" => $msg,
        ]);
    }

    public function store_examples(Request $request){

        $id = 0;
        $msg = "";

        do {

            // get user id
            $user = $request->get("tp_example_user_id", null);
            if ($user){
                $userId = json_decode($user)[0]->id;
            } else {
                $msg = "Unknown user id";
                break;
            }

            $id = $request->get("tp_example_id", 0);
            if ($id > 0){
                // update existing label
                $example = Example::where('id', $id)
                    ->where('user_id', $userId)
                    ->first();
                if ($example == null){
                    $msg = "Unable to find example " . $id;
                    break;
                }
                $done = $example->done;
                $opType = " has been updated";
            } else {
                // add new label
                $example = new Example;
                $opType = " has been added";
                $done = false;
            }

            // populate values
            $example->user_id = $userId;
            $example->example = $request->get("tp_example_example","<unknown>");
            $example->translation = $request->get("tp_example_translation","<unknown>");
            $example->save();

            // update relationship
            $example->cards()->sync($request->get("tp_example_cards", []));

            // generate audio file
            AudioController::generateAudioFile(AudioController::EXAMPLE, $example->id, $example->example);

            // generate data to be returned
            $msg = "Example " . $example->example . " has been " . $opType;
            $id = $example->id;

        } while(0);

        return Response::json([
            "id" => $id,
            "message" => $msg,
        ]);
    }

    public function delete_examples(Request $request){

        $id = 0;
        $msg = "";

        do {

            // get user id
            $user = $request->get("tp_example_delete_user_id", null);
            if ($user){
                $userId = json_decode($user)[0]->id;
            } else {
                $msg = "Unknown user id";
                break;
            }

            // get label id
            $id = $request->get("tp_example_delete_id", 0);
            if ($id < 1){
                $msg = "Card id is missing";
                break;
            }

            // search example
            $example = Example::where('id', $id)->where('user_id', $userId)->first();
            if ($example == null){
                $msg = "Unable to find example id " . $id;
                $id = 0;
                break;
            }

            // remove relationship and delete label
            $example->cards()->detach();
            $example->delete();

            // Remove audio file
            AudioController::deleteAudioFile(AudioController::EXAMPLE, $example->id);

            // create return message
            $msg = 'Card "' . $example->symbol . '" has been deleted';

        } while (0);

        return Response::json([
            "id" => $id,
            "message" => $msg,
        ]);
    }

    private function getLabels($filters){

        // get labels
        $labels = Label::withCount('cards')->with('cards.examples')->where('user_id', $filters['userId']);
        if (count($filters["labelIds"]) > 0){
            $labels = $labels->wherein('id', $filters["labelIds"]);
        }
        if (count($filters["cardIds"]) > 0){
            $labels = $labels->whereHas('cards', function($query) use ($filters) {
                $query
                    ->where('card.user_id', $filters["userId"])
                    ->wherein('card.id', $filters["cardIds"]); 
            });
        }
        if (count($filters["exampleIds"]) > 0){
            $labels = $labels->whereHas('cards.examples', function($query) use ($filters) {
                $query
                    ->where('example.user_id', $filters["userId"])
                    ->wherein('example.id', $filters["exampleIds"]); 
            });
        }
        return [
            'data' => $labels,
            'priv' => null,
        ];
    }

    private function getCards($filters){

        // get cards
        $cards = Card::withCount(['labels', 'examples'])->where('user_id', $filters['userId']);
        if (count($filters['cardIds']) > 0){
            $cards = $cards->wherein('id', $filters['cardIds']); 
        }
        if (count($filters['labelIds']) > 0){
            $cards = $cards->whereHas('labels', function($query) use ($filters) {
                $query
                    ->where('label.user_id', $filters["userId"])
                    ->wherein('label.id', $filters['labelIds']); 
            });
        }
        if (count($filters['exampleIds']) > 0){
            $cards = $cards->whereHas('examples', function($query) use ($filters) {
                $query
                    ->where('example.user_id', $filters["userId"])
                    ->wherein('example.id', $filters['exampleIds']); 
            });
        }
        return [
            'data' => $cards,
            'priv' => null,
        ];
    }

    private function getExamples($filters){

        // get examples
        $examples = Example::withCount('cards')->with('cards.labels')->where('user_id', $filters["userId"]);
        if (count($filters["exampleIds"]) > 0){
            $examples = $examples->wherein('id', $filters["exampleIds"]); 
        }
        if (count($filters["cardIds"]) > 0){
            $examples = $examples->whereHas('cards', function($query) use ($filters) {
                $query
                    ->where('card.user_id', $filters["userId"])
                    ->wherein('card.id', $filters["cardIds"]); 
            });
        }
        if (count($filters["labelIds"]) > 0){
            $examples = $examples->whereHas('cards.labels', function($query) use ($filters) {
                $query
                    ->where('label.user_id', $filters["userId"])
                    ->wherein('label.id', $filters["labelIds"]); 
            });
        }

        return [
            'data' => $examples,
            'priv' => null,
        ];
    }

    private function getQuiz($filters){

        $cards = $this->getCards($filters)["data"]; 
        $countTotal = $cards->count();

        $remain = $cards->where('done', false);
        $countRemain = $remain->count();

        // No cards left in the stack => quiz is over
        if ($countRemain == 0){
            return [
                'data' => $remain,
                'priv' => [
                    'card' => null,
                    'labels' => '',
                    'url' => '',
                    'total' => $countTotal,
                    'remain' => $countRemain,
                ],
            ];
        }

        // get random card
        $card = $remain->offset(random_int(0, $countRemain-1))->first();

        // get audio file
        $url = "";
        $path = AudioController::getAudioFilePath(AudioController::CARD, $card->id);
        if (file_exists($path["fs"])){
            $url = $path["url"];
        }

        // get examples
        $examples = Example::with('cards')->whereHas('cards', function($query) use ($card) {
            $query->where('card.id', $card->id); 
        });

        return [
            'data' => $examples,
            'priv' => [
                'card' => $card,
                'labels' => $card->labels->pluck('label'),
                'url' => $url,
                'total' => $countTotal,
                'remain' => $countRemain,
            ],
        ];
    }

    public function datatable(Request $request)
    {

        $begin = microtime(true);

        $columns = $request->get('columns');
        $start = $request->get("start");
        $length = $request->get("length");
        $search = $request->get("search")["value"];
        $order = $request->get("order");
        $draw = $request->get('draw');

        $orderCol = $columns[$order[0]["column"]]["data"];
        $orderDir = $order[0]["dir"];

        // get app context
        $app = $request->get("tpApp");
        $filters = [
            'userId' => array_column(json_decode($request->get("tpUserData")), 'id')[0],
            'labelIds' => array_column(json_decode($request->get("tpLabelData")), 'id'),
            'cardIds' => array_column(json_decode($request->get("tpCardData")), 'id'),
            'exampleIds' => array_column(json_decode($request->get("tpExampleData")), 'id'),
        ];

        // set card to done
        $doneId = $request->get("tpDoneData");
        if ($doneId){
            $card = Card::find($doneId);
            $card->done = true;
            $card->save();
        }

        // reset cards
        $reset = $request->get("tpResetData");
        if ($reset === "reset"){
            $this->getCards($filters)["data"]->where('done', true)->update(['done' => false]);
        }

        $req = null;
        switch($app){
        case MainController::MAIN_LABEL:
            $tmp = $this->getLabels($filters);
            $req = $tmp["data"];
            $priv = $tmp["priv"];
            break;
        case MainController::MAIN_CARD:
            $tmp = $this->getCards($filters);
            $req = $tmp["data"];
            $priv = $tmp["priv"];
            break;
        case MainController::MAIN_EXAMPLE:
            $tmp = $this->getExamples($filters);
            $req = $tmp["data"];
            $priv = $tmp["priv"];
            break;
        case MainController::MAIN_QUIZ:
            $tmp = $this->getQuiz($filters);
            $req = $tmp["data"];
            $priv = $tmp["priv"];
            break;
        default:
            return Response::json([]);
        }

        error_log('before count' . strval( microtime(true) - $begin));

        // total items
        $total = $req->count();

        // filter items
        if (strlen($search) > 0){
            $req = $req->where(function($query) use ($search, $columns){
                $first = true;
                foreach($columns as $column){
                    if ($column["searchable"] == true){
                        if ($first){
                            $query = $query->where($column["data"], 'LIKE', '%' .$search . '%');
                            $first = false;
                        } else {
                            $query = $query->orWhere($column["data"], 'LIKE', '%' .$search . '%');
                        }
                    }
                }
            });
        }

        error_log('before filtered count' . strval( microtime(true) - $begin));
        // total filter cards
        $totalFiltered = $req->count();

        if ($orderCol == 'labels'){
            $req = $req->orderBy('labels_count', $orderDir);
        } elseif ($orderCol == 'examples'){
            $req = $req->orderBy('examples_count', $orderDir);
        } elseif ($orderCol == 'cards'){
            $req = $req->orderBy('cards_count', $orderDir);
        } else {
            $req = $req->orderBy($orderCol, $orderDir);
        }

        // get paginated data
        if ($length > 0){
            $req = $req->offset($start)->limit($length);
        }

        error_log('before sql' . strval( microtime(true) - $begin));

        $req = $req->get();
        error_log('after sql' . strval( microtime(true) - $begin));

        $data = [];
        foreach($req as $row){
            $item = [];
            foreach ($columns as $column){
                switch($column["data"]){
                case "symbol":
                    $audioPath = AudioController::getAudioFilePath(AudioController::CARD, $row->id);
                    $item["symbol"] = [
                        "symbol" => $row->symbol,
                        "url" => (file_exists($audioPath['fs']) ? $audioPath['url'] : ''),
                    ];
                    break;
                case "example":
                    $audioPath = AudioController::getAudioFilePath(AudioController::EXAMPLE, $row->id);
                    $item["example"] = [
                        "example" => $row->example,
                        "url" => (file_exists($audioPath['fs']) ? $audioPath['url'] : ''),
                    ];
                    break;
                case "labels":
                    $item["labels"] = [
                        //"text" => htmlentities(implode(', ', $row->labels->pluck('label')->toArray())),
                        //"count" => $row->labels_count,
                        "data" => $row->labels->pluck('label', 'id')->toArray(),
                    ];
                    break;
                case "cards":
                    $item["cards"] = [
                        //"text" => '', //htmlentities(implode(', ', $row->cards->pluck('symbol')->toArray())),
                        //"count" => $row->cards_count,
                        "data" => $row->cards->pluck('symbol', 'id')->toArray(),
                    ];
                    break;
                case "examples":
                    $item["examples"] = [
                        "text" => htmlentities(implode(', ', $row->examples->pluck('example')->toArray())),
                        //"text" => '',
                        //"count" => $row->examples_count,
                        "data" => $row->examples->pluck('example', 'id')->toArray(),
                    ];
                    break;
                case "action":
                    $item["action"] = '';
                    break;
                default:
                    $item[$column["data"]] = $row->{$column["data"]};
                }
            }
            $data[] = $item;
        }

        error_log('after processing ' . strval( microtime(true) - $begin));

        $response = [
            "draw" => intval($draw),
            "iTotalRecords" => $total,
            "iTotalDisplayRecords" => $totalFiltered,
            "aaData" => $data,
            "priv" => $priv,
        ];

        return Response::json($response);

    }

    public function autocomplete(Request $request){


        $type = $request->get('selectType');
        if (!is_string($type)){
            return Response::json([]);
        }

        $userId = json_decode($request->get('userData'));
        if (isset($userId[0]->id)){
            $userId = $userId[0]->id;
        } else {
            return Response::json([]);
        }

        $search = $request->get('searchData');
        if (!is_string($search)){
            $search = "";
        }

        switch($type){
        case "label":
            $query = Label::select(['id', 'label'])->where('user_id', $userId);
            $field = "label";
            break;
        case "card":
            $query = Card::select(['id', 'symbol'])->where('user_id', $userId);
            $field = "symbol";
            break;
        case "example":
            $query = Example::select(['id', 'example'])->where('user_id', $userId);
            $field = "example";
            break;
        case "user":
            $query = User::select('id', 'name');
            $field = "name";
            break;
        default:
            return Response::json([]);
        }

        if (strlen($search) > 0){
            $query = $query->where($field, 'LIKE', '%' . $search . '%');
        }

        $results = $query->get();

        $data = [];
        foreach($results as $result){
            $data[] = [
                'id' => $result->id,
                'text' => $result->{$field},
            ];

        }
        return Response::json($data);
    }
}
