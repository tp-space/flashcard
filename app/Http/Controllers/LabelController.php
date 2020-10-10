<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Label;

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
    public function index()
    {
        //
        $labels = Label::orderBy('id', 'DESC')->get();
        return view('labels', compact('labels'));
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

        return redirect('/labels')->with('success', 'Label "' . $label->label  . '" has been deleted');
    }
}
