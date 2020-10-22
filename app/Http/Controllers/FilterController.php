<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FilterController extends Controller
{
    //
    public function setSingleFilter($source, $id, $target)
    {
        $var = 'filter_' . $source .  '_ids';
        session([$var => [$id]]);
        return redirect($target);
    }

    public function clearAllFilters(Request $request){

        session([
            'filter_card_ids' => [],
            'filter_label_ids' => [],
            'filter_example_ids' => [],
        ]);

        return redirect($request->get('tp_url'));
    }

    public function setAllFilters(Request $request){

        $cardIds = $request->get('tp_filter_card', []);
        $labelIds = $request->get('tp_filter_label', []);
        $exampleIds = $request->get('tp_filter_example', []);

        error_log(json_encode($labelIds));
        session([
            'filter_card_ids' => $cardIds,
            'filter_label_ids' => $labelIds,
            'filter_example_ids' => $exampleIds,
        ]);

        return redirect($request->get('tp_url'));
    }

}
