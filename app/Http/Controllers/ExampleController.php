<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Card;
use App\Models\Label;
use App\Models\Example;
use App\Models\User;

class ExampleController extends Controller
{

    private function populateRecord($data, $request){

        // populate record with data in request
        $data->example = $request->get("tp_example","<unknown>");
        $data->translation = $request->get("tp_translation","<unknown>");
        $data->user_id = $request->session()->get('filter_user_ids');

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
        $exampleIds = session()->get('filter_example_ids', []);
        $cardIds = session()->get('filter_card_ids', []);
        $labelIds = session()->get('filter_label_ids', []);
        $userIds = session()->get('filter_user_ids', 0);

        // Make sure user filter is set
        if ($userIds == 0){
            $userIds = Auth::id();
            FilterController::sessionSetFilter('user', $userIds);
        }

        // get examples
        $examples = Example::with(['cards', 'cards.labels'])->where('user_id', $userIds);
        if (count($exampleIds) > 0){
            $examples = $examples->wherein('id', $exampleIds); 
        }
        if (count($cardIds) > 0){
            $examples = $examples->whereHas('cards', function($query) use ($cardIds) {
                $query->wherein('card.id', $cardIds); 
            });
        }
        if (count($labelIds) > 0){
            $examples = $examples->whereHas('cards.labels', function($query) use ($labelIds) {
                $query->wherein('label.id', $labelIds); 
            });
        }
        $examples = $examples->orderBy('id', 'DESC')->get();

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

        return view('examples', compact(
            'examples',
            'filterCards',
            'filterLabels',
            'filterExamples',
            'filterUsers'
        ));
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

        $example = new Example;
        $example = $this->populateRecord($example, $request);
        $example->save();

        // update relationship
        $example->cards()->sync($request->get("tp_cards", "[]"));

        // generate audio file
        AudioController::generateAudioFile(AudioController::EXAMPLE, $example->id, $example->example);

        // clear session filters
        FilterController::sessionClearFilter();

        return redirect('/examples/' . $example->id)
            ->with('success', 'New example "' . $example->example . '" has been added');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        session(['example_id' => $id]);
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

        $example = Example::findOrFail($id);
        $example = $this->populateRecord($example, $request);
        $isDirty = $example->isDirty('example');
        $example->save();

        // update relationship
        $example->cards()->sync($request->get("tp_cards", "[]"));

        // clear session filters
        FilterController::sessionClearFilter();

        // generate audio file
        if ($isDirty){
            AudioController::generateAudioFile(AudioController::EXAMPLE, $example->id, $example->example);
        }
        return redirect('/examples/' . $id)
            ->with('success', 'Example "' . $example->example . '" has been changed');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $example = Example::findOrFail($id);
        $example->delete();

        // update relationship
        $example->cards()->detach();

        // Remove audio file
        AudioController::deleteAudioFile(AudioController::EXAMPLE, $example->id);

        return redirect('/examples')->with('success', 'Example "' . $example->example . '" has been deleted');

    }
}
