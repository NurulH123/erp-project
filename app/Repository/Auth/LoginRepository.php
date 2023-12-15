<?php
namespace App\Repository\Auth;

interface LoginRepository 
{
    public function login($request);
}