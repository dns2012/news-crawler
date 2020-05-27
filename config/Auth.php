<?php 

namespace Config;

class Auth
{
    public static function authenticated($request)
    {
        $token = explode(' ', getallheaders()['Authorization']);

        if (! empty($token[1]) && $token[0] === 'Bearer' && $token[1] === getenv('token')) {
            return true;
        }
        
        return Response::failure('You are not authenticated !', 403);
    }
}