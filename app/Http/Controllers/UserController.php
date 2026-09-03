<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $role = $request->input('role');
        $sortBy = in_array($request->input('sort_by'), ['name', 'email', 'role', 'created_at']) ? $request->input('sort_by') : 'created_at';
        $sortDir = strtolower($request->input('sort_dir')) === 'asc' ? 'asc' : 'desc';

        $query = User::query();

        $query->when($search, function ($q, $search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('role', 'like', "%{$search}%");
        });

        $query->when($role, function ($q, $role) {
            $q->where('role', $role);
        });

        $users = $query->orderBy($sortBy, $sortDir)->paginate(10)->withQueryString();

        return view('users.index', compact('users', 'search', 'role', 'sortBy', 'sortDir'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function show(User $user)
    {
        return redirect()->route('users.edit', $user);
    }

    public function store(UserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        User::create($data);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        if (auth()->id() === $user->id || $user->email === 'admin@arms.test') {
            return redirect()->route('users.index')->with('error', 'The default Admin account is protected and cannot be edited.');
        }

        return view('users.edit', compact('user'));
    }

    public function update(UserRequest $request, User $user)
    {
        if (auth()->id() === $user->id || $user->email === 'admin@arms.test') {
            return redirect()->route('users.index')->with('error', 'The default Admin account is protected and cannot be edited.');
        }

        $data = $request->validated();

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id || $user->email === 'admin@arms.test') {
            return redirect()->route('users.index')->with('error', 'The default Admin account is protected and cannot be deleted.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
