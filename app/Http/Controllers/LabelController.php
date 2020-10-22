<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;
use App\Models\Label;
use App\Models\Example;

class LabelController extends Controller
{

    private function populateRecord($data, $request){

        // populate record with data in request
        $data->label = $request->get("tp_label","<unknown>");

        return $data;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        // extract filter
        $labelIds = $request->session()->get('filter_label_ids', []);
        $cardIds = $request->session()->get('filter_card_ids', []);

        // get labels
        $labels = Label::with('cards');
        if (count($labelIds) > 0)
        {
            $labels = $labels->wherein('id', $labelIds);
        }
        if (count($cardIds) > 0)
        {
            $labels = $labels->whereHas('cards', function($query) use ($cardIds) {
                $query->wherein('card.id', $cardIds); 
            });
        }
        $labels = $labels->orderBy('id', 'DESC')->get();

        // get data for filters
        $filterCards = Card::select('id', 'symbol')->orderBy('id', 'DESC')->get();
        $filterLabels = Label::select('id', 'label')->orderBy('id', 'DESC')->get();
        $filterExamples = Example::select('id', 'example')->orderBy('id', 'DESC')->get();

        return view('labels', compact('labels', 'filterCards', 'filterLabels', 'filterExamples'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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
        $label = new Label;
        $label = $this->populateRecord($label, $request);
        $label->save();

        // update relationship
        $label->cards()->sync($request->get("tp_cards", "[]"));

        return redirect('/labels/' . $label->id)->with('success', 'New "' .  $label->label .'" label has been added');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        session(['label_id' => $id]);
        return $this->index($request);
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
        $label = Label::findOrFail($id);
        $label = $this->populateRecord($label, $request);
        $label->save();

        // update relationship
        $label->cards()->sync($request->get("tp_cards", "[]"));

        return redirect('/labels/' . $id)->with('success', 'Label "' . $label->label . '" has been changed');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $label = Label::findOrFail($id);
        $label->delete();

        // update relationship
        $label->cards()->detach();

        return redirect('/labels')->with('success', 'Label "' . $label->label  . '" has been deleted');
    }

}
