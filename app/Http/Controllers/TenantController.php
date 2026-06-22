<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\User;
use Session;

class TenantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $this->authorizeSuperAdmin();

        $tenants = Tenant::latest()->paginate(10);

        return view('platform.tenants.index', compact('tenants'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $this->authorizeSuperAdmin();

        return view('platform.tenants.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $this->authorizeSuperAdmin();

        $request->validate([
            'name' => 'required',
            'subdomain' => 'required|alpha_dash|unique:tenants', // Added restriction
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Added image validation
            'admin_name' => 'required',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => 'required|min:8',
        ]);

        if ($request->hasFile('image')) 
        {
            $fileName = $request->name .' - '. now() . '.' . $request->file('image')->extension();
            $path = $request->file('image')->storeAs('tenant-logos', $fileName, 'public');
            // File will be stored in storage/app/public/tenant-logos with the custom filename
            //return $path;
        }

        // 1. Create Tenant
        $tenant = Tenant::create([
            'name' => $request->name,
            'subdomain' => strtolower($request->subdomain),
            'logo' => $path,
        ]);

        // 2. Create Client Admin
        User::create([
            'tenant_id' => $tenant->id,
            'role' => 'client_admin',
            'name' => $request->admin_name,
            'email' => $request->admin_email,
            'password' => Hash::make($request->admin_password),            
        ]);

        Session::flash('success', 'Tenant created successfully.');

        return redirect()->route('tenants.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tenant $tenant)
    {
        //
        return view('platform.tenants.show', compact('tenants'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //
        $this->authorizeSuperAdmin();

        $tenant = Tenant::findOrFail($id);
        return view('platform.tenants.edit', compact('tenant'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
        $this->authorizeSuperAdmin();

        $tenant = Tenant::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'subdomain' => 'required|unique:tenants,subdomain,' . $tenant->id,
        ]);

        if($request->hasFile('image')) 
        {
            $fileName = $request->name .' - '. now() . '.' . $request->file('image')->extension();
            $path = $request->file('image')->storeAs('tenant-logos', $fileName, 'public');
            // File will be stored in storage/app/public/tenant-logos with the custom filename
            //return $path;
            $tenant->update([
                'name' => $request->name,
                'subdomain' => strtolower($request->subdomain),
                'logo' => $path,
            ]);
        } else {
            $tenant->update([
                'name' => $request->name,
                'subdomain' => strtolower($request->subdomain),
            ]);
        }

        Session::flash('success', 'Tenant updated successfully.');

        return redirect()->route('tenants.index');
    }

    /**
     * Toggle tenant status (active/inactive).
     */
    public function toggleStatus($id)
    {
        $this->authorizeSuperAdmin();

        $tenant = Tenant::findOrFail($id);
        $tenant->status = !$tenant->status;
        $tenant->save();

        $status = $tenant->status ? 'activated' : 'deactivated';
        Session::flash('success', "Tenant {$status} successfully.");

        return redirect()->route('tenants.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tenant $tenant)
    {
        //
        $this->authorizeSuperAdmin();

        $tenant->delete();

        return back()->with('success', 'Tenant deleted.');
    }

    private function authorizeSuperAdmin()
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }
    }

    public function storeImages($request, $file_name)
    {
        // if ($request->hasFile('image')) 
        // {
        //     $fileName = $file_name . '.' . $request->file('image')->extension();
        //     $path = $request->file('image')->storeAs('public/images', $fileName, 'public');
        //     // File will be stored in storage/app/public/images with the custom filename
        //     return $path;
        // }

        // if ($request->hasFile('image')) {
        //     $path = $request->file('image')->store('images','public');
        //     // File will be stored in storage/app/images
        //     // $path will contain the relative path with the generated filename
        //     // Save $path to your database
        //     return $path;
        // }
        // Handle no file case

        // if ($request->hasFile('image')) {
        //     $path = $request->file('image')->store('images', 'public');
        //     // File will be stored in storage/app/public/images
        //     // $path will contain the relative path with the generated filename
        //     // You can generate a public URL using Storage::url($path)
        //     return $path;
        // }
    }
}
