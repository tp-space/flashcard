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

    private static function getQuizData($state) {

        // get quiz stack size
        $cards = MainController::getCards($state);
        $countAll = $cards->count();

        // quiz over ?
        $id = $state["tp_quiz_id"];
        if ($id == null){
            return [ 'symbol' => "", 'pinyin' => "", 'translation' => "", 'comment' => "", 'labels' => [], 'url' => "",
                'stats' => [
                    'total'=> $countAll,
                    'remaining' => 0,
                    
                ]
            ];
        }

        // get remaining cards stack size
        $remain = $cards->where('done', false);
        $countRemain = $remain->count();

        // get current card from remaining stack
        $card = $remain->where('id', $id)->first();
        
        // get audio file
        $url = "";
        $path = AudioController::getAudioFilePath(AudioController::CARD, $card->id);
        if (file_exists($path["fs"])){
            $url = $path["url"];
        }

        return [
            'symbol' => $card->symbol,
            'pinyin' => $card->pinyin,
            'translation' => $card->translation,
            'comment' => $card->comment,
            'labels' => $card->labels->pluck('label'),
            'url' => $url,
            'stats' => [
                'total'=> $countAll,
                'remaining' => $countRemain,
            ]
        ];
    }

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

        if (key_exists("tp_quiz_state", $state)){
            $newState["tp_quiz_state"] = $state["tp_quiz_state"];
        }

        if (key_exists("tp_quiz_shown", $state)){
            $newState["tp_quiz_shown"] = $state["tp_quiz_shown"];
        }

        // ajust quiz card

        // get the remaining cards 
        $remain = MainController::getCards($newState)->where('done', false);
        $countRemain = $remain->count();

        if ($countRemain == 0){
            // No cards left in the stack => quiz is over
            $newState["tp_quiz_id"] = null;
        } else {

            // get current card from remaining stack
            $card = $remain->where('id', intval($state["tp_quiz_id"]))->first();

            if ($card == null){
                //card not found => get new card from remaining stack
                $card = MainController::getCards($newState)
                    ->where('done', false)
                    ->offset(random_int(0, $countRemain-1))
                    ->first();
                $newState["tp_quiz_id"] = $card->id;
            } else {
                // card still in the stack => no need to change it
                $newState["tp_quiz_id"] = $state["tp_quiz_id"] ;
            }
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
            'tp_quiz_id' => null,
            'tp_quiz_state' => 'Show',
            'tp_quiz_shown' => ['tp-toggle-symbol'],
        ];
    }

    public static function getState(){
        return session()->get('tp_state', self::getDefaultState());
    }

    public function setSession(Request $request){

        $data = $request->get('tp_data');
        $state = self::validateState($data);
        session(['tp_state' => $state]);

        return $this->getSession();

    }

    public function getSession(){

        $state = self::getState();

        if ($state["tp_app"] == 'quiz'){
            $quiz = self::getQuizData($state);
        } else {
            $quiz = null;
        }

        $data = [
            'tp_status' => 'success',
            'tp_state_ids' => $state,
            'tp_state_data' => [
                'tp_user_data' => User::find($state["tp_user_id"])->name,
                'tp_label_data' => Label::whereIn('id', $state["tp_label_ids"])->pluck('label'), 
                'tp_card_data' => Card::whereIn('id', $state["tp_card_ids"])->pluck('symbol'), 
                'tp_example_data' => Example::whereIn('id', $state["tp_example_ids"])->pluck('example'), 
                'tp_quiz_data' => $quiz,
            ]
        ];
        return Response::json($data);
    }
}


