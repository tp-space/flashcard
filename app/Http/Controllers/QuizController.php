<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;
use App\Models\Label;
use App\Models\Example;

class QuizController extends Controller
{
    //
    public function index()
    {
        // extract filter
        $cardIds = session()->get('filter_card_ids', []);
        $labelIds = session()->get('filter_label_ids', []);
        $exampleIds = session()->get('filter_example_ids', []);

        // get cards
        $cards = Card::with('labels')->with('examples');
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
        $cards = $cards->orderBy('id', 'DESC')->get();
        
        // get a single card
        $card = $cards[1];


        // get data for filters
        $filterCards = Card::select('id', 'symbol')->orderBy('id', 'DESC')->get();
        $filterLabels = Label::select('id', 'label')->orderBy('id', 'DESC')->get();
        $filterExamples = Example::select('id', 'example')->orderBy('id', 'DESC')->get();

        return view('quiz', compact('card', 'filterCards', 'filterLabels', 'filterExamples'));

    }

    public function update(Request $request, $id)
    {

        $card = Card::findOrFail($id);
        $card->done = true;
        $card->save();

        return $this->index();

    }
}
