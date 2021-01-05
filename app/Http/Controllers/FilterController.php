<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Label;
use App\Models\Card;
use App\Models\Example;
use App\Models\User;
use Response;

class FilterController extends Controller
{
    protected const DEF_APP = MainController::MAIN_LABEL;

    private static function validateState($state){

        $newState = self::getDefaultState();

        if (false == is_array($state)){
            return $newState;
        }

        if (key_exists("tp_app", $state) && in_array($state["tp_app"], MainController::appList())){
            $newState["tp_app"] = $state["tp_app"];
        }

        if (key_exists("tp_user_id", $state) && in_array($state["tp_user_id"], MainController::userList())){
            $newState["tp_user_id"] = $state["tp_user_id"];
        }

        if (key_exists("tp_label_ids", $state)){
            $newState["tp_label_ids"] = $state["tp_label_ids"];
        }

        if (key_exists("tp_card_ids", $state)){
            $newState["tp_card_ids"] = $state["tp_card_ids"];
        }

        if (key_exists("tp_example_ids", $state)){
            $newState["tp_example_ids"] = $state["tp_example_ids"];
        }

        return $newState;

    }

    private static function getDefaultState(){
        return [
            'tp_app' => self::DEF_APP,
            'tp_user_id' => Auth::id(),
            'tp_card_ids' => [],
            'tp_label_ids' => [],
            'tp_example_ids' => [],
            'tp_quiz_hidden' => [],
            'tp_quiz_id' => 969,
        ];
    }

    public static function getState(){
        return session()->get('tp_state', self::getDefaultState());
    }

    public function setSession(Request $request){

        $data = $request->get('tp_data');
        $state = self::validateState($data);
        session(['tp_state' => $state]);

        return Response::json(['tp_status' => 'success', 'tp_data' => $state]);

    }

    public function getSession(){
        $state = self::getState();
        $url = "";
        if ($state["tp_quiz_id"] > 0){
            $path = AudioController::getAudioFilePath(AudioController::CARD, $state["tp_quiz_id"]);
            if (file_exists($path["fs"])){
                $url = $path["url"];
            }
        }
        $card = null;
        $labels = [];
        $tmp = Card::with('labels')->where('id', $state["tp_quiz_id"])->first();
        //$tmp = Card::with(['labels' => function ($query) {$query->select('label.id', 'label.label');}])->where('card.id', $state["tp_quiz_id"])->first(['id', 'symbol', 'pinyin', 'tanslation', 'comment']);
        if ($tmp){
            //$labels = $tmp->labels->toArray();
            $card = [
                'symbol' => $tmp->symbol,
                'pinyin' => $tmp->pinyin,
                'translation' => $tmp->translation,
                'comment' => $tmp->comment,
            ];
            $labels = $tmp->labels->pluck('label');
        }
        $data = [
            'tp_status' => 'success',
            'tp_state_ids' => $state,
            'tp_state_data' => [
                'tp_user_data' => User::find($state["tp_user_id"])->name,
                'tp_label_data' => Label::whereIn('id', $state["tp_label_ids"])->pluck('label'), 
                'tp_card_data' => Card::whereIn('id', $state["tp_card_ids"])->pluck('symbol'), 
                'tp_example_data' => Example::whereIn('id', $state["tp_example_ids"])->pluck('example'), 
                'tp_quiz_data' => $card,
                'tp_quiz_label' => $labels,
                'tp_quiz_url' => $url,
            ]
        ];
        return Response::json($data);
    }
}


