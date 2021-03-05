<?php

namespace App\Http\Controllers\Admin;

use App\Imports\FileImport;
use App\UploadList;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class UploadListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $lists = UploadList::all();
        return view('admin.list.index', compact('lists'));
    }
 
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.list.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'upload_file' => ['required', 'mimes:csv,xls,xlsx'],
            'active' => ['in:on']
        ]);

        $file = $request->upload_file->storeAs('files', $request->upload_file->getClientOriginalName());

        $uploadFile = new UploadList;
        $uploadFile->name = $request->name;
        $uploadFile->file_name = $request->upload_file->getClientOriginalName();
        $uploadFile->file_path = $file;
        $uploadFile->active = $request->has('active') ? true : false;
        $uploadFile->save();

        try {
            Excel::import(new FileImport($uploadFile->id), $file);
        } catch (ValidationException $e) {
            $failures = $e->failures();
            $uploadFile->delete();
            return back()->with('failures', $failures);
        }

        return redirect()->route('list.index');

    }

    /**
     * Display the specified resource.
     *
     * @param UploadList $uploadList
     * @return Response
     */
    public function show(UploadList $uploadList)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param UploadList $list
     * @return Response
     */
    public function edit(UploadList $list)
    {
        return view('admin.list.edit', compact('list'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param UploadList $list
     * @return Response
     */
    public function update(Request $request, UploadList $list)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'active' => ['in:on']
        ]);

        if($request->has('active') && $request->active == "on" && UploadList::where('active', true)->get()->count() > 0) {
            return redirect()->route('list.index')->with('status', 'One of the list already active.');
        }

        $list->update($request->all());
        return redirect()->route('list.index')->with('status', 'List has been updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param UploadList $list
     * @return Response
     */
    public function destroy(UploadList $list)
    {
        try {
            $list->numbers()->delete();
            $list->delete();
            return back()->with('status', 'List has been deleted along with all the numbers.');
        } catch (Exception $e) {
            return back()->with('status', $e->getMessage());
        }
    }
}
