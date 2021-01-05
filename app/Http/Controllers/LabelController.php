<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Card;
use App\Models\Label;
use App\Models\Example;
use App\Models\User;

class LabelController extends Controller
{

    private function populateRecord($data, $request){

        // populate record with data in request
        $data->label = $request->get("tp_label","<unknown>");
        $data->user_id = session()->get('filter_user_ids');

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
        $labelIds = session()->get('filter_label_ids', []);
        $cardIds = session()->get('filter_card_ids', []);
        $exampleIds = session()->get('filter_example_ids', []);
        $userIds = session()->get('filter_user_ids', 0);

        // Make sure user filter is set
        if ($userIds == 0){
            $userIds = Auth::id();
            FilterController::sessionSetFilter('user', $userIds);
        }

        // get labels
        $labels = Label::with(['cards', 'cards.examples'])->where('user_id', $userIds);
        if (count($labelIds) > 0){
            $labels = $labels->wherein('id', $labelIds);
        }
        if (count($cardIds) > 0){
            $labels = $labels->whereHas('cards', function($query) use ($cardIds) {
                $query->wherein('card.id', $cardIds); 
            });
        }
        if (count($exampleIds) > 0){
            $labels = $labels->whereHas('cards.examples', function($query) use ($exampleIds) {
                $query->wherein('example.id', $exampleIds); 
            });
        }
        $labels = $labels->orderBy('id', 'DESC')->get();
        $filterUsers = User::select('id', 'name')->orderBy('id', 'DESC')->get();
        $withFilters = true;

        return view('labels', compact('labels', 'withFilters', 'filterUsers'));
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

        // clear session filters
        FilterController::sessionClearFilter();

        return redirect('/labels/' . $label->id)->with('success', 'New "' .  $label->label .'" label has been added');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        session(['label_id' => $id]);
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
        $label = Label::findOrFail($id);
        $label = $this->populateRecord($label, $request);
        $label->save();

        // update relationship
        $label->cards()->sync($request->get("tp_cards", "[]"));

        // clear session filters
        FilterController::sessionClearFilter();

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
