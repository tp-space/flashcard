<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Card;
use App\Models\User;

use Response;

class CardController extends Controller
{

    private function populateRecord($data, $request){

        // populate record with data in request
        $data->symbol = $request->get("tp_symbol","<unknown>");
        $data->pinyin = $request->get("tp_pinyin","<unknown>");
        $data->translation = $request->get("tp_translation","<unknown>");
        $data->comment = $request->get("tp_comment","<unknown>");
        $data->user_id = $request->session()->get('filter_user_ids');
        $data->done = False;

        if (is_null($data->comment)){
            $data->comment = '';
        }

        return $data;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        // Make sure user filter is set
        $userIds = session()->get('filter_user_ids', 0);
        if ($userIds == 0){
            $userIds = Auth::id();
            FilterController::sessionSetFilter('user', $userIds);
        }

        $filterUsers = User::select('id', 'name')->orderBy('id', 'DESC')->get();
        $withFilters = true;

        return view('cards', compact('withFilters', 'filterUsers'));

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

        // generate audio file
        AudioController::generateAudioFile(AudioController::CARD, $card->id, $card->symbol);

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
        $isDirty = $card->isDirty('symbol');
        $card->save();

        // update relationship
        $card->labels()->sync($request->get("tp_labels", "[]"));
        $card->examples()->sync($request->get("tp_examples", "[]"));

        // generate audio file
        if ($isDirty){
            AudioController::generateAudioFile(AudioController::CARD, $card->id, $card->symbol);
        }

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

        // Remove audio file
        AudioController::deleteAudioFile(AudioController::CARD, $card->id);

        return redirect('/cards')->with('success', 'Card "' . $card->symbol . '" has been deleted');

    }

    public function paginationCards(Request $request)
    {

        $columns = $request->get('columns');
        $start = $request->get("start");
        $length = $request->get("length");
        $search = $request->get("search")["value"];
        $order = $request->get("order");

        $orderCol = $columns[$order[0]["column"]]["data"];
        $orderDir = $order[0]["dir"];

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
        $cards = Card::withCount(['labels', 'examples'])->where('user_id', $userIds);
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

        // total cards
        $total = $cards->count();

        // filter cards
        if (strlen($search) > 0){
            $cards = $cards->where(function($query) use ($search){
                $query->where('symbol', 'LIKE', '%' .$search . '%')
                    ->orWhere('pinyin', 'LIKE', '%' .$search . '%')
                    ->orWhere('translation', 'LIKE', '%' .$search . '%')
                    ->orWhere('comment', 'LIKE', '%' .$search . '%');
            });
        }

        // total filter cards
        $totalFiltered = $cards->count();

        if ($orderCol == 'labels'){
            $cards = $cards->orderBy('labels_count', $orderDir);
        } elseif ($orderCol == 'examples'){
            $cards = $cards->orderBy('examples_count', $orderDir);
        } else {
            $cards = $cards->orderBy($orderCol, $orderDir);
        }

        // get paginated data
        $cards = $cards->offset($start)->limit($length)->get();

        $data = [];
        foreach($cards as $card){
            $audioPath = AudioController::getAudioFilePath(AudioController::CARD, $card->id);
            $data[] = [
                "id" => $card->id,
                "symbol" => [
                    "symbol" => $card->symbol,
                    "url" => (file_exists($audioPath['fs']) ? $audioPath['url'] : ''),
                ],
                "pinyin" => $card->pinyin,
                "translation" => $card->translation,
                "comment" => $card->comment,
                "labels" => [
                    "ids" => $card->labels->pluck('id')->toArray(),
                    "text" => htmlentities(implode(', ', $card->labels->pluck('label')->toArray())),
                    "count" => $card->labels_count,
                ],
                "examples" => [
                    "ids" => $card->examples->pluck('id')->toArray(),
                    "text" => htmlentities(implode(', ', $card->examples->pluck('example')->toArray())),
                    "count" => $card->examples_count,
                ],
                "action" => '',
            ];
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

}
