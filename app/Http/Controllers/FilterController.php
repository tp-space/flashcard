<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Label;
use Response;

class FilterController extends Controller
{

    public static function sessionClearFilter(){
        session([
            'filter_card_ids' => [],
            'filter_label_ids' => [],
            'filter_example_ids' => [],
        ]);
    }

    public static function sessionSetFilter($source, $id){

        // clear all filters
        self::sessionClearFilter();

        // set the selected filter
        $var = 'filter_' . $source .  '_ids';
        session([$var => ($source == 'user' ? $id : [$id])]);

    }

    public function setSingleFilter($source, $id, $target)
    {
        self::sessionSetFilter($source, $id);
        return redirect($target);
    }

    public function clearAllFilters(Request $request){
        self::sessionClearFilter();
        return redirect($request->get('tp_url'));
    }

    public function setAllFilters(Request $request){

        // get currently selected user 
        $oldUserIds = $request->session()->get('filter_user_ids');

        $userIds = $request->get('tp_filter_user', 0);
        $cardIds = $request->get('tp_filter_card', []);
        $labelIds = $request->get('tp_filter_label', []);
        $exampleIds = $request->get('tp_filter_example', []);

        session([
            'filter_card_ids' => $cardIds,
            'filter_label_ids' => $labelIds,
            'filter_example_ids' => $exampleIds,
            'filter_user_ids' => $userIds,
        ]);

        // clear filter if clear-button has been pressed or if selected user filter has been changed
        if (($request->has('tp_filter_clear')) || ($oldUserIds != $userIds)){
            self::sessionClearFilter();
        }

        return redirect($request->get('tp_url'));

    }

    public function autocomplete(Request $request){
        $search = $request->get('search');
        $results = Label::select(['id', 'label'])->where('label', 'LIKE', '%' . $search . '%')->get();
        $data = [];
        foreach($results as $result){
            $data[] = [
                'id' => $result->id,
                'text' => $result->label,
            ];

        }
        return Response::json($data);
    }
}
