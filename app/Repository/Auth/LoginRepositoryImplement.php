<?php
namespace App\Repository\Auth;

use App\Repository\Services\Login\AuthService;

class LoginRepositoryImplement
{
    private $loginRepository;

    public function with($type)
    {
        $class = [
            'auth' => new AuthService(),
        ];              

        $this->loginRepository = $class[$type];

        return $this->loginRepository;
    }
    
}