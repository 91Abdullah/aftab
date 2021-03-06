<?php

namespace App\Http\Controllers\Admin;

use App\Role;
use App\User;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // aor

    private $remove_existing = "yes";
    private $max_contact = 1;

    // end aor

    // auth

    private $auth_type = "userpass";

    // end auth

    // endpoint

    private $context = "default";
    private $disallow = "all";
    // private $allow = "opus,ulaw";
    private $allow = "alaw,ulaw";
    private $dtls_auto_generate_cert = "yes";
    private $webrtc = "yes";
    private $use_avpf = "yes";
    private $media_encryption = "dtls";
    private $dtls_verify = "fingerprint";
    private $dtls_setup = "actpass";
    private $ice_support = "no";
    private $media_use_received_transport = "yes";
    private $rtcp_mux = "yes";
    private $transport = "transport-wss";

    // end endpoint

    /**
     * Display a listing of reporting users
     */
    public function reportingIndex()
    {
        $users = Role::where('name', 'reporter')->first()->users;
        return view('admin.user.reporting_user', compact('users'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = Role::where('name', 'agent')->first()->users;
        return view('admin.user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.user.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(Role::where('name', 'agent')->first()->users()->count() <= 20) {
            if($request->has('reporter')) {
                $request->validate([
                    'name' => ['required', 'string', 'max:255'],
                    'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                    'password' => ['required', 'string', 'min:8', 'confirmed'],
                ]);
                event(new Registered($user = $this->make($request->all())));
                $role = Role::where('name', 'reporter')->first();
                $user->roles()->attach($role->id);
                return redirect()->route('user.reporting')->with('status', 'Reporting User created.');
            }

            $this->validator($request->all())->validate();
            event(new Registered($user = $this->make($request->all())));

            $role = Role::where('name', 'agent')->first();
            $user->roles()->attach($role->id);

            DB::table('ps_aors')->insert([
                'id' => $request->agent_id,
                'max_contacts' => $this->max_contact,
                'remove_existing' => $this->remove_existing
            ]);
            DB::table('ps_auths')->insert([
                'id' => $request->agent_id,
                'auth_type' => $this->auth_type,
                'password' => $request->agent_password,
                'username' => $request->agent_id
            ]);
            DB::table('ps_endpoints')->insert([
                'id' => $request->agent_id,
                'transport' => $this->transport,
                'aors' => $request->agent_id,
                'auth' => $request->agent_id,
                'context' => $this->context,
                'disallow' => $this->disallow,
                'allow' => $this->allow,
                'dtls_auto_generate_cert' => $this->dtls_auto_generate_cert,
                'webrtc' => $this->webrtc,
                'use_avpf' => $this->use_avpf,
                'media_encryption' => $this->media_encryption,
                'dtls_verify' => $this->dtls_verify,
                'dtls_setup' => $this->dtls_setup,
                'ice_support' => $this->ice_support,
                'media_use_received_transport' => $this->media_use_received_transport,
                'rtcp_mux' => $this->rtcp_mux
            ]);

            DB::table('endpoint_user')->insert([
                'ps_endpoint_id' => $request->agent_id,
                'user_id' => $user->id
            ]);

            return redirect()->route('user.index')->with('status', 'Agent has been created.');
        } else {
            abort(503);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('admin.user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        $this->update_validator($request->except('password', 'password_confirmation'), $user)->validate();
        if($request->has('password') && !empty($request->password)) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        $user->update($request->except('password', 'password_confirmation'));
        $user->auths()->update(['id' => $request->agent_id, 'username' => $request->agent_id, 'password' => $request->agent_password]);
        $user->aors()->update(['id' => $request->agent_id]);
        $user->endpoints()->update(['id' => $request->agent_id, 'aors' => $request->agent_id, 'auth' => $request->agent_id]);
        DB::table('endpoint_user')->where('user_id', $user->id)->update(['ps_endpoint_id' => $request->agent_id]);
        return redirect()->route('user.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        try {
            $user->delete();
            return redirect()->route('user.index')->with('status', 'User has been deleted.');
        } catch (Exception $e) {
            return redirect()->route('user.index')->with('status', "User deletion failed with reason: $e->getMessage()");
        }
    }

    public function validateAgentId(Request $request)
    {
        $endpoints = DB::table('ps_endpoints')->where('id', $request->agent_id)->get();
        if(count($endpoints) > 0) {
            return response()->json( "error: ID found in database.", 400);
        } else {
            return response()->json("success: ID not found in database.", 200);
        }
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'agent_id' => ['required', 'unique:ps_endpoints,id', 'numeric'],
            'agent_password' => ['required'],
        ]);
    }

    protected function update_validator(array $data, User $user)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['sometimes', 'string', 'min:8', 'confirmed'],
            'agent_id' => [Rule::unique('ps_endpoints', 'id')->ignore($data['agent_id'], 'id'), 'numeric'],
        ]);
    }

    protected function make(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
}
