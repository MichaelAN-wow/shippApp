<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Company;
use App\Models\TeamManagement;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;


class UserController extends Controller
{
    //
    public function index()
    {

        $users = Auth::user()->type == 'super_admin' ? User::all() : User::where('company_id', session('company_id'))->get();
        
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:5|confirmed',
            'type' => 'required|in:unauthorized,employee,admin,super_admin',
        ]);
        $validatedData['password'] = Hash::make($validatedData['password']);
        User::create($validatedData);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:4|confirmed',
            'type' => 'required|in:unauthorized,employee,admin,super_admin',
        ]);
        if ($request->filled('password')) {
            $validatedData['password'] = Hash::make($request->password);
        } else {
            unset($validatedData['password']);
        }

        $user->update($validatedData);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        TeamManagement::where('user_id', $id)->delete();
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    public function updateAccount(Request $request, User $user)
    {
        
        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'nullable|string|min:4|confirmed',
            'type' => 'nullable|in:unauthorized,employee,admin,super_admin',
            'company_name' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:16096',
        ]);
        
        // Update the user's name
        $user->name = $request->input('name');
        $user->type = $request->input('type');

        // Update the password only if provided
        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }
        $user->save();
        
        if ($user->type === 'admin' || $user->type === 'super_admin') {
            
            $company = $user->company;
            
            // Update company name if provided
            if ($request->filled('company_name')) {
                $company->name = $request->input('company_name');
            }

            // Handle company logo upload
            if ($request->hasFile('photo')) {
                // Delete the old logo if it exists
                if ($company->logo_url && Storage::disk('public')->exists($company->logo_url)) {
                    Storage::disk('public')->delete($company->company_logo);
                }
                
                // Store the new logo
                $filePath = $request->file('photo')->store('company_logos', 'public');
                $company->logo_url = $filePath;
            }
    
            // Save company details
            $company->save();
        }
    

        return redirect()->back()->with('success', 'Account updated successfully.');
    }

    public function activate($token)
    {
        $decodedToken = base64_decode($token);
        [$email, $name, $companyName, $expiry] = explode('##', $decodedToken);
        
        // Find the user by activation token
        $user = User::where('activation_token', $token)->first();

        if (!$user) {
            return redirect('/login')->with('error', 'Invalid activation token.');
        }

        $company = Company::create([
            'name' => $companyName ?: 'OHS',
        ]);

        // Activate the user's account
        $user->email_verified_at = Carbon::now();
        $user->activation_token = null; // Clear the token after activation
        $user->type = 'admin';
        $user->company_id = $company->id;
        $user->save();

        return redirect('/login')->with('success', 'Your account has been activated. You can now log in.');
    }
}
