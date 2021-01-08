<?php

namespace App\Http\Controllers;

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
        return view('main');
    }

    private static function getLabels($state){

        // get labels
        $labels = Label::withCount('cards')->with('cards.examples')->where('user_id', $state['tp_user_id']);
        if (count($state["tp_label_ids"]) > 0){
            $labels = $labels->wherein('id', $state["tp_label_ids"]);
        }
        if (count($state["tp_card_ids"]) > 0){
            $labels = $labels->whereHas('cards', function($query) use ($state) {
                $query->wherein('card.id', $state["tp_card_ids"]); 
            });
        }
        if (count($state["tp_example_ids"]) > 0){
            $labels = $labels->whereHas('cards.examples', function($query) use ($state) {
                $query->wherein('example.id', $state["tp_example_ids"]); 
            });
        }
        return $labels;
    }

    public static function getCards($state){

        // get cards
        $cards = Card::withCount(['labels', 'examples'])->where('user_id', $state['tp_user_id']);
        if (count($state['tp_card_ids']) > 0){
            $cards = $cards->wherein('id', $state['tp_card_ids']); 
        }
        if (count($state['tp_label_ids']) > 0){
            $cards = $cards->whereHas('labels', function($query) use ($state) {
                $query->wherein('label.id', $state['tp_label_ids']); 
            });
        }
        if (count($state['tp_example_ids']) > 0){
            $cards = $cards->whereHas('examples', function($query) use ($state) {
                $query->wherein('example.id', $state['tp_example_ids']); 
            });
        }
        return $cards;
    }

    private static function getExamples($state){

        // get examples
        $examples = Example::withCount('cards')->with('cards.labels')->where('user_id', $state["tp_user_id"]);
        if (count($state["tp_example_ids"]) > 0){
            $examples = $examples->wherein('id', $state["tp_example_ids"]); 
        }
        if (count($state["tp_card_ids"]) > 0){
            $examples = $examples->whereHas('cards', function($query) use ($state) {
                $query->wherein('card.id', $state["tp_card_ids"]); 
            });
        }
        if (count($state["tp_label_ids"]) > 0){
            $examples = $examples->whereHas('cards.labels', function($query) use ($state) {
                $query->wherein('label.id', $state["tp_label_ids"]); 
            });
        }

        return $examples;
    }

    private static function getQuiz($state){

        // get examples
        $examples = Example::withCount('cards')->with('cards.labels')->where('user_id', $state["tp_user_id"]);
        if (count($state["tp_example_ids"]) > 0){
            $examples = $examples->wherein('id', $state["tp_example_ids"]); 
        }
        $cardId = $state["tp_quiz_id"];
        if ((count($state["tp_card_ids"]) > 0) && (false == in_array($state["tp_quiz_id"], $state["tp_card_ids"]))){
            $cardId = 0;
        }
        $examples = $examples->whereHas('cards', function($query) use ($cardId) {
            $query->where('card.id', $cardId); 
        });
        if (count($state["tp_label_ids"]) > 0){
            $examples = $examples->whereHas('cards.labels', function($query) use ($state) {
                $query->wherein('label.id', $state["tp_label_ids"]); 
            });
        }

        return $examples;
    }

    public function pagination(Request $request)
    {

        $columns = $request->get('columns');
        $start = $request->get("start");
        $length = $request->get("length");
        $search = $request->get("search")["value"];
        $order = $request->get("order");

        $orderCol = $columns[$order[0]["column"]]["data"];
        $orderDir = $order[0]["dir"];

        // extract state
        $state = FilterController::getState();

        $req = null;
        switch($state["tp_app"]){
        case MainController::MAIN_LABEL:
            $req = self::getLabels($state);
            break;
        case MainController::MAIN_CARD:
            $req = self::getCards($state);
            break;
        case MainController::MAIN_EXAMPLE:
            $req = self::getExamples($state);
            break;
        case MainController::MAIN_QUIZ:
            $req = self::getQuiz($state);
            break;
        default:
            return Response::json([]);
        }


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

        $req = $req->get();

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
                        "ids" => $row->labels->pluck('id')->toArray(),
                        "text" => htmlentities(implode(', ', $row->labels->pluck('label')->toArray())),
                        "count" => $row->labels_count,
                    ];
                    break;
                case "cards":
                    $item["cards"] = [
                        "ids" => $row->cards->pluck('id')->toArray(),
                        "text" => htmlentities(implode(', ', $row->cards->pluck('symbol')->toArray())),
                        "count" => $row->cards_count,
                    ];
                    break;
                case "examples":
                    $item["examples"] = [
                        "ids" => $row->examples->pluck('id')->toArray(),
                        "text" => htmlentities(implode(', ', $row->examples->pluck('example')->toArray())),
                        "count" => $row->examples_count,
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

        $draw = $request->get('draw');

        $response = [
            "draw" => intval($draw),
            "iTotalRecords" => $total,
            "iTotalDisplayRecords" => $totalFiltered,
            "aaData" => $data,
        ];

        return Response::json($response);

    }

    public function autocomplete(Request $request){
        $search = $request->get('searchData', '');
        $type = $request->get('searchType', '');

        switch($type){
        case "label":
            $query = Label::select(['id', 'label']);
            $field = "label";
            break;
        case "card":
            $query = Card::select(['id', 'symbol']);
            $field = "symbol";
            break;
        case "example":
            $query = Example::select(['id', 'example']);
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
