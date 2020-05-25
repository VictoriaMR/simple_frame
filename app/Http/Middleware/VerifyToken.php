<?php

namespace App\Http\Middleware;

/**
 * 验证token.
 */
class VerifyToken
{
    /**
     * The URIs that should be excluded from token verification.
     *
     * @var array
     */
    protected static $except = [
        'Admin/Index/loginIn',
    ];

    protected static $exceptNotToken = [

    ];

    protected static $exceptNotAgreement = [

    ];

    /**
     * Handle an incoming request.
     * @return mixed
     */
    public static function handle($request)
    {
        $param = array_pop($request);
        $route = implode('//', $request);

        if (self::inExceptArray($route)) {
            return true;
        }

        switch ($request['Class']) {
            case 'Admin':
                // dd($_SESSION);
                break;
            
            default:
                # code...
                break;
        }

        return true;
    }

    /**
     * Determine if the request has a URI that should pass through token verification.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    protected static function inExceptArray($route)
    {
        return in_array(str_replace('\\', '/', $route), self::$except);
    }

    protected static function inExceptByNotTokenArray($request)
    {
        foreach (self::$exceptNotToken as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->is($except)) {
                return true;
            }
        }

        return false;
    }

    protected static function inExceptByNotAgreementArray($request)
    {
        foreach (self::$exceptNotAgreement as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->is($except)) {
                return true;
            }
        }

        return false;
    }

}
