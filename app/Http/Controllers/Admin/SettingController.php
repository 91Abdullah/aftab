<?php

namespace App\Http\Controllers\Admin;

use App\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::query()->where('key', '!=', 'random_type')->get();
        return view('admin.setting.index', compact('settings'));
    }

    public function update(Request $request, Setting $setting)
    {
        $request->validate([
            'server_address' => ['required','ipv4'],
            'wss_comm_port' => ['required','digits:4'],
            'wss_socket_port' => ['required','digits:4'],
            'auto_answer' => ['nullable','in:true,false'],
            'random_mode' => ['required','in:true,false'],
            'random_type' => ['required', 'in:list,number']
        ]);

        //return dd($request->all());

        foreach ($request->all() as $key => $value) {
            $setting->where('key', $key)
                ->update(['value' => $value]);
        }

        return redirect()->route('setting.index');
    }
}
