<?php

namespace App\Http\Controllers\Admin;

use App\ResponseCode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class ResponseCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $codes = ResponseCode::all();
        return view('admin.responseCode.index', compact('codes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.responseCode.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['string', 'required'],
            'code' => ['numeric', 'required', 'unique:response_codes,code']
        ]);

        $code = ResponseCode::create($request->all());
        return redirect()->route('responseCode.index')->with('status', 'Response code has been created.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ResponseCode  $responseCode
     * @return \Illuminate\Http\Response
     */
    public function show(ResponseCode $responseCode)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ResponseCode  $responseCode
     * @return \Illuminate\Http\Response
     */
    public function edit(ResponseCode $responseCode)
    {
        return view('admin.responseCode.edit', compact('responseCode'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ResponseCode  $responseCode
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ResponseCode $responseCode)
    {
        $request->validate([
            'name' => ['string', 'required'],
            'code' => ['numeric', 'required', Rule::unique('response_codes', 'code')->ignore($responseCode->id)]
        ]);

        $responseCode->update($request->all());
        return redirect()->route('responseCode.index')->with('status', 'Response code has been updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ResponseCode  $responseCode
     * @return \Illuminate\Http\Response
     */
    public function destroy(ResponseCode $responseCode)
    {
        try {
            $responseCode->delete();
        } catch (\Exception $e) {
            redirect()->route('responseCode.index')->with('status', 'Deletion failed: ' . $e->getMessage() . '.');
        }
        return redirect()->route('responseCode.index')->with('status', 'Response code has been deleted.');
    }
}
