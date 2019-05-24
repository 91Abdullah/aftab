<?php

namespace App\Http\Controllers\Admin;

use App\ListNumber;
use App\UploadList;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SubListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(UploadList $parent)
    {
        $lists = $parent->numbers()->get();
        return view('admin.sublist.index', compact('lists'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ListNumber  $listNumber
     * @return \Illuminate\Http\Response
     */
    public function show(ListNumber $listNumber)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ListNumber  $listNumber
     * @return \Illuminate\Http\Response
     */
    public function edit(ListNumber $listNumber)
    {
        return view('admin.sublist.edit', compact('listNumber'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ListNumber  $listNumber
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ListNumber $listNumber)
    {
        $request->validate([
            'number' => ['required','regex:/(03)[0-9]{9}/']
        ]);
        $listNumber->update($request->all());
        return redirect()->route('sublist.index', ['parent' => $listNumber->parent()->first()])->with('status', 'Number updated in the database.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ListNumber  $listNumber
     * @return \Illuminate\Http\Response
     */
    public function destroy(ListNumber $listNumber)
    {
        try {
            $listNumber->delete();
            return back()->with('status', 'Number deleted from database.');
        } catch (Exception $e) {
            return back()->with('status', $e->getMessage());
        }
    }
}
