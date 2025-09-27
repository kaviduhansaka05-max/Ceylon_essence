<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\User;

class CustomerController extends BaseController
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $users = User::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->orderBy('name')
            ->paginate(15)
            ->appends(['q' => $q]);

        return view('admin.layout.customers', compact('users', 'q'));
    }
public function toggleStatus(User $user)
{
    $user->status = $user->status === 'active' ? 'inactive' : 'active';
    $user->save();

    return back()->with('success', "User {$user->name} is now {$user->status}.");
}

    public function block(User $user)
    {
        $user->update(['status' => 'blocked']);
        return back()->with('success', 'User has been blocked.');
    }

    public function unblock(User $user)
    {
        $user->update(['status' => 'active']);
        return back()->with('success', 'User has been unblocked.');
    }
}
