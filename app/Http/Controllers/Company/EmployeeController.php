<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public function index()
    {
        $user = User::find(Auth::id());
        $employees = $user->company->employee();

        return response()->json(['data' => $employees]);
    }

    public function create(Request $request)
    {
        
    }
}
