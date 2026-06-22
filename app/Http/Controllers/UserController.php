<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Tenant;
use Session;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $users = User::where('role','client_admin')->latest()->paginate(10);
        return view('platform.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $tenants = Tenant::where('status', 1)->get();
        return view('platform.users.create', compact('tenants'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'role' => 'required|in:super_admin,client_admin',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
        ]);

        // Conditional validation for tenant_id
        if ($request->role === 'client_admin') {
            $request->validate([
                'tenant_id' => 'required|exists:tenants,id',
            ]);
        }

        // Generate random password
        $password = Str::random(12);
        
        // Create User
        $userData = [
            'role' => $request->role,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($password),
            'email_verified_at' => null,
        ];

        // Only add tenant_id for client_admin
        if ($request->role === 'client_admin') {
            $userData['tenant_id'] = $request->tenant_id;
        }

        $user = User::create($userData);

        // Send verification email
        $this->sendVerificationEmail($user, $password);

        $roleText = $request->role === 'super_admin' ? 'Super Admin' : 'Client Admin';
        Session::flash('success', "{$roleText} user created successfully. A verification email has been sent to " . $user->email);

        return redirect()->route('users.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //
        $user = User::findOrFail($id);
        $tenants = Tenant::where('status', 1)->get();
        return view('platform.users.edit', compact('user', 'tenants'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
        $user = User::findOrFail($id);
        
        $request->validate([
            'role' => 'required|in:super_admin,client_admin',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8',
        ]);

        // Conditional validation for tenant_id
        if ($request->role === 'client_admin') {
            $request->validate([
                'tenant_id' => 'required|exists:tenants,id',
            ]);
        }

        $updateData = [
            'role' => $request->role,
            'name' => $request->name,
            'email' => $request->email,
        ];

        // Only add tenant_id for client_admin
        if ($request->role === 'client_admin') {
            $updateData['tenant_id'] = $request->tenant_id;
        } else {
            $updateData['tenant_id'] = null;
        }

        // Only update password if provided
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        Session::flash('success', 'User updated successfully.');

        return redirect()->route('users.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Send verification email to user
     */
    private function sendVerificationEmail($user, $password)
    {
        $verificationUrl = route('verification.verify', ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]);
        
        $data = [
            'user' => $user,
            'password' => $password,
            'verificationUrl' => $verificationUrl,
        ];

        try {
            Mail::send('emails.user-verification', $data, function($message) use ($user) {
                $message->to($user->email)
                        ->subject('Verify Your Email Address - Set Your Password');
            });
        } catch (\Exception $e) {
            // Log error but don't fail the user creation
            \Log::error('Failed to send verification email: ' . $e->getMessage());
        }
    }
}
