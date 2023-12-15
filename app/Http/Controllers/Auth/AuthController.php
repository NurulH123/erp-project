<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Repository\Auth\LoginRepositoryImplement;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private  $loginRepository;

    public function __construct(LoginRepositoryImplement $login)
    {
        $this->loginRepository = $login;
    }

    public function login(Request $request)
    {
        $class = $request->class;
        $this->loginRepository->with($class)->login($request);
    }
}
