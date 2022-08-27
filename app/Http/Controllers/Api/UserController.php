<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Helpers\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    use Role;
    public function index(Request $request): JsonResponse {
        if ($this->isAdmin($request->user()) || $this->isOperator($request->user())) {
            $users = User::all();
            if ($users == null){
                return $this->onError(404, 'Users Not Found');
            } else
            {
                return $this->onSuccess($users, 'Ok');
            }
        }
        return $this->onError(401, 'Unauthorized');
    }

    public function store(Request $request): JsonResponse {
        if ($this->isAdmin($request->user())) {
            $fields = $request->validate([
                'name' => 'required|string',
                'role'=>[
                    'required',
                    Rule::in(['operator', 'delivery_service']),
                ],
                'email' => 'required|string|unique:users,email',
                'password' => 'required|string|confirmed'
            ]);
            $user = User::create([
                'name' => $fields['name'],
                'role' => $fields['role'],
                'email' => $fields['email'],
                'password' => bcrypt($fields['password'])
            ]);
            return $this->onSuccess($user, 'User Created');
        }
        return $this->onError(401, 'Unauthorized');
    }

    public function show(Request $request, $id): JsonResponse {
        if ($this->isAdmin($request->user()) || $this->isOperator($request->user())) {
            $user = User::find($id);
            if ($user === null)
            {
                return $this->onError(404, 'User Not Found');
            } else
            {
                return $this->onSuccess($user, 'OK');
            }
        }
        return $this->onError(401, 'Unauthorized');
    }

    public function update(Request $request, $id): JsonResponse {
        if ($this->isAdmin($request->user())) {
            $fields = $request->validate([
                'name' => 'required|string',
                'email' => 'required|string|unique:users,email',
                'password' => 'required|string|confirmed'
            ]);
            $user = User::find($id);
            $user->update([
                'name' => $fields['name'],
                'email' => $fields['email'],
                'password' => bcrypt($fields['password'])
            ]);
            return $this->onSuccess($user, 'User Updated');
        }
        return $this->onError(401, 'Unauthorized');
    }

    public function destroy(Request $request, $id): JsonResponse {
        if ($this->isAdmin($request->user())) {
            $user = User::find($id);
            if ($user === null)
            {
                return $this->onError(404, 'User Not Found');
            } else
            {
                if ($user->role === 'admin'){
                    return $this->onError(403, 'Forbidden');
                } else
                {
                    $user->delete();
                    return $this->onSuccess([], 'User Deleted');
                }
            }
        }
        return $this->onError(401, 'Unauthorized');

    }
}
