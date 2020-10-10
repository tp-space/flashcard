<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Example;

class ExampleController extends Controller
{

    private function populateRecord($data, $request){

        // populate record with data in request
        $data->example = $request->get("tp_example","<unknown>");
        $data->translation = $request->get("tp_translation","<unknown>");

        return $data;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $examples = Example::orderBy('id', 'DESC')->get();
        return view('examples', compact('examples'));

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
        $example->save();

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

        return redirect('/examples')->with('success', 'Example "' . $example->symbol . '" has been deleted');

    }
}
