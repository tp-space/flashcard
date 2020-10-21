<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;
use App\Models\Label;

class CardController extends Controller
{

    private function populateRecord($data, $request){

        // populate record with data in request
        $data->symbol = $request->get("tp_symbol","<unknown>");
        $data->pinyin = $request->get("tp_pinyin","<unknown>");
        $data->translation = $request->get("tp_translation","<unknown>");
        $data->example = "";
        $data->comment = "";
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

        $cards = Card::orderBy('id', 'DESC')->get();
        $labels = Label::orderBy('label')->get();
        return view('cards', compact('cards', 'labels'));

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

        return redirect('/cards')->with('success', 'Card "' . $card->symbol . '" has been deleted');

    }
}
