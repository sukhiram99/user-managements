<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Gate;
use Auth;
use App\Models\Role;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UpdateRoleRequest extends FormRequest
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
            $role = Role::findOrFail(request()->route('role')->id);
        } catch (ModelNotFoundException $e) {
            throw ValidationException::withMessages(['role' => 'Role not found.']);
        }

         return [
            'name' => 'required|unique:roles,name,' . $role->id,
            'slug' => 'required|unique:roles,slug,' . $role->id . '|alpha_dash',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ];
    }

   
}