<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;
use App\Models\Label;
use App\Models\Example;

class CardController extends Controller
{

    private function populateRecord($data, $request){

        // populate record with data in request
        $data->symbol = $request->get("tp_symbol","<unknown>");
        $data->pinyin = $request->get("tp_pinyin","<unknown>");
        $data->translation = $request->get("tp_translation","<unknown>");
        $data->comment = $request->get("tp_comment","<unknown>");
        $data->example = "";
        $data->done = False;

        return $data;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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

        // get data for filters
        $filterCards = Card::select('id', 'symbol')->orderBy('id', 'DESC')->get();
        $filterLabels = Label::select('id', 'label')->orderBy('id', 'DESC')->get();
        $filterExamples = Example::select('id', 'example')->orderBy('id', 'DESC')->get();

        return view('cards', compact('cards', 'filterCards', 'filterLabels', 'filterExamples'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // Same as store, but uses GET => Not needed
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $card = new Card;
        $card = $this->populateRecord($card, $request);
        $card->save();

        // update relationship
        $card->labels()->sync($request->get("tp_labels", "[]"));
        $card->examples()->sync($request->get("tp_examples", "[]"));

        // clear session filters
        FilterController::sessionClearFilter();

        return redirect('/cards/' . $card->id)->with('success', 'New card "' . $card->symbol . '" has been added');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        session(['card_id' => $id]);
        return $this->index();

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Same as update, but uses GET => Not needed
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $card = Card::findOrFail($id);
        $card = $this->populateRecord($card, $request);
        $card->save();

        // update relationship
        $card->labels()->sync($request->get("tp_labels", "[]"));
        $card->examples()->sync($request->get("tp_examples", "[]"));

        // clear session filters
        FilterController::sessionClearFilter();

        return redirect('/cards/' . $id)->with('success', 'Card "' . $card->symbol . '" has been changed');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $card = Card::findOrFail($id);
        $card->delete();

        // update relationship
        $card->labels()->detach();
        $card->examples()->detach();

        return redirect('/cards')->with('success', 'Card "' . $card->symbol . '" has been deleted');

    }
}
