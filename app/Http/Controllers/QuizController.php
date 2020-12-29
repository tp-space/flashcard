<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Card;
use App\Models\Label;
use App\Models\Example;
use App\Models\User;

class QuizController extends Controller
{
    //
    public function index()
    {
        // extract filter
        $cardIds = session()->get('filter_card_ids', []);
        $labelIds = session()->get('filter_label_ids', []);
        $exampleIds = session()->get('filter_example_ids', []);
        $userIds = session()->get('filter_user_ids', 0);

        // Make sure user filter is set
        if ($userIds == 0){
            $userIds = Auth::id();
            FilterController::sessionSetFilter('user', $userIds);
        }

        // get cards
        $cards = Card::with('labels')->with('examples')->where('user_id', $userIds);
        if (count($cardIds) > 0){
            $cards = $cards->wherein('id', $cardIds); 
        }
        if (count($labelIds) > 0)
        {
            $cards = $cards->whereHas('labels', function($query) use ($labelIds) {
                $query->wherein('label.id', $labelIds); 
            });
        }
        if (count($exampleIds) > 0)
        {
            $cards = $cards->whereHas('examples', function($query) use ($exampleIds) {
                $query->wherein('example.id', $exampleIds); 
            });
        }
        $countAll = $cards->count();
        $remain = $cards->where('done', false)->get();
        $countRemain = count($remain);
        
        // get a single card
        $card = null;
        $max = count($remain);
        if ($max > 0) {
            $select = random_int(0,$max-1);
            $card = $remain[$select];
        }

        // get data for filters
        $filterCards = Card::select('id', 'symbol')
            ->where('user_id', $userIds)
            ->orderBy('id', 'DESC')
            ->get();
        $filterLabels = Label::select('id', 'label')
            ->where('user_id', $userIds)
            ->orderBy('id', 'DESC')
            ->get();
        $filterExamples = Example::select('id', 'example')
            ->where('user_id', $userIds)
            ->orderBy('id', 'DESC')
            ->get();
        $filterUsers = User::select('id', 'name')->orderBy('id', 'DESC')->get();

        return view('quiz', compact(
            'card',
            'filterCards',
            'filterLabels',
            'filterExamples',
            'countAll',
            'countRemain',
            'filterUsers',
        ));

    }

    public function setDone($id)
    {
        $card = Card::findOrFail($id);
        $card->done = true;
        $card->save();

        return redirect('/quiz');
    }

    public function reset()
    {

        $userIds = session()->get('filter_user_ids');

        // Make sure user filter is set
        if ($userIds == 0){
            $userIds = Auth::id();
            FilterController::sessionSetFilter('user', $userIds);
        }

        Card::where('done', '!=', false)
            ->where('user_id', $userIds)
            ->update(['done' => false]);

        return redirect('/quiz');
    }

    public function updateState(Request $request){

        $key = $request->input('key');
        $state = $request->input('state');

        session([
            $key => $state
        ]);
        return [
            'status' => 'success',
            'data' => []
        ];
    }
}
