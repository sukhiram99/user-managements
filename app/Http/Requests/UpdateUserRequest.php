<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Gate;
use Auth;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
         try {
            $user = User::findOrFail(request()->route('user')->id);
        } catch (ModelNotFoundException $e) {
            throw ValidationException::withMessages(['user' => 'User not found.']);
        }

         return [
             'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8|confirmed',
            'remark' => 'nullable|string|max:500',
            'roles'    => 'required|array',
            'roles.*'  => 'exists:roles,id',
        ];
    }

   
}