<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Gate;
use Auth;
use App\Models\Permission;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UpdatePermissionRequest extends FormRequest
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
            $permission = Permission::findOrFail(request()->route('permission')->id);
        } catch (ModelNotFoundException $e) {
            throw ValidationException::withMessages(['permission' => 'Permission not found.']);
        }

         return [
            'name' => 'required|string|max:255',
            'slug' => 'required|alpha_dash|unique:permissions,slug,' . $permission->id,
            'description' => 'nullable|string',
        ];
    }

   
}