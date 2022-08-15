<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class SSOService
{
    public function queryUserForLogin(string $username, string $password)
    {
        return DB::table('users')
            ->where('username', $username)
            ->where('password', '=', base64_encode(md5($password, true)))
            ->first();
    }
}
