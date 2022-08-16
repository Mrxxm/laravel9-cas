<?php

namespace App\Http\Controllers;

use App\Services\SSOService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class SSOController extends Controller
{
    const REDIS_TMP_TICKET = 'redis_tmp_ticket';     // 临时票据(一次性)
    const COOKIE_USER_TICKET = 'cookie_user_ticket'; // 获取用户全局票据
    const REDIS_USER_TICKET = 'redis_user_ticket';   // 用户全局票据获取用户id
    const REDIS_USER_TOKEN = 'redis_user_token';     // 用户信息

    protected SSOService $service;

    public function __construct(SSOService $service)
    {
        $this->service = $service;
    }

    public function login(Request $request)
    {
        // 1.获取票据，如果cookie中能够获取到，证明用户登录过
        $user_ticket = $this->getCookie($request, self::COOKIE_USER_TICKET);

        Log::debug('login接口 user_ticket：' . $user_ticket);

        $is_verified = $this->verifyUserTicket($user_ticket);

        if ($is_verified) {
            $tmp_ticket = $this->createTmpTicket();

            return redirect($request->get('returnUrl') . '?tmpTicket=' . $tmp_ticket);
        }

        return view('login', ['returnUrl' => $request->get('returnUrl'), 'errmsg' => '']);
    }

    public function doLogin(Request $request, Response $response)
    {
        $username = $request->get('username') ?? '';
        $password = $request->get('password') ?? '';

        // 0.前置判断
        if (!$username || !$password) {
            return view('login', ['returnUrl' => '', 'errmsg' => '用户名或密码不能为空']);
        }
        // 1.用户名密码校验
        $user = $this->service->queryUserForLogin($username, $password);
        if (!$user) {
            return view('login', ['returnUrl' => '', 'errmsg' => '用户名或密码不正确']);
        }
        // 2.实现用户redis会话
        $unique_token = random_int(0, 999999999) . time();
        $user->unique_token = $unique_token;
        Redis::set(self::REDIS_USER_TOKEN . ':' . $user->id, json_encode($user));

        // 3.生成全局门票
        $user_ticket = random_int(0, 999999999999) . time();
        $cookie = $this->setCookie(self::COOKIE_USER_TICKET, $user_ticket, $response);
        Redis::set(self::REDIS_USER_TICKET . ':' . $user_ticket, $user->id);

        // 4.生成临时票据
        $tmp_ticket = $this->createTmpTicket();

        return redirect($request->get('returnUrl') . '?tmpTicket=' . $tmp_ticket)->withCookie($cookie);
    }

    public function verifyTmpTicket(Request $request)
    {
        $tmp_ticket_value = Redis::get(self::REDIS_TMP_TICKET . ':' . ($request->get('tmpTicket') ?? ''));

        if (!$tmp_ticket_value) {
            return response()->json('用户票据异常1');
        }
        if (md5($request->get('tmpTicket')) != $tmp_ticket_value) {
            return response()->json('用户票据异常2');
        } else {
            Redis::expire(self::REDIS_TMP_TICKET . ':' . $request->get('tmpTicket'), 0);
        }

        $user_ticket = $this->getCookie($request, self::COOKIE_USER_TICKET);

        Log::debug("verify user ticket:" . $user_ticket);

        $user_id = Redis::get(self::REDIS_USER_TICKET . ':' . $user_ticket);

        if (!$user_id) {
            return response()->json('用户票据异常3');
        }

        $user_info_redis = Redis::get(self::REDIS_USER_TOKEN . ':' . $user_id);
        $user_info = json_decode($user_info_redis);

        if (!$user_info) {
            return response()->json('用户票据异常4');
        }

        return response()->json($user_info);
    }

    public function logout(Request $request)
    {
        $user_ticket = $this->getCookie($request, self::COOKIE_USER_TICKET);

        $cookie = $this->delCookie(self::COOKIE_USER_TICKET);

        $user_id = Redis::get(self::REDIS_USER_TOKEN . ':' . $user_ticket);
        Redis::expire(self::REDIS_USER_TOKEN . ':' . $user_ticket, 0);

        Redis::expire(self::REDIS_USER_TOKEN . ':' . $user_id, 0);

        return response()->json('ok')->withCookie($cookie);
    }

    /**
     * 校验全局票据
     *
     * @param string $user_ticket
     *
     * @return bool
     */
    private function verifyUserTicket(string $user_ticket): bool
    {
        if (!$user_ticket) {
            return false;
        }
        $user_id = Redis::get(self::REDIS_USER_TICKET . ':' . $user_ticket);
        if (!$user_id) {
            return false;
        }
        $user_info = Redis::get(self::REDIS_USER_TOKEN . ':' . $user_id);
        if (!$user_info) {
            return false;
        }

        return true;
    }

    /**
     * 创建临时票据
     *
     * @return string
     */
    private function createTmpTicket(): string
    {
        try {
            $tmp_ticket = random_int(0, 99999999) . time();

            Redis::set(self::REDIS_TMP_TICKET . ':' . $tmp_ticket, md5($tmp_ticket), 600);
        } catch (\Exception $e) {
            Log::error($e);
        }

        return $tmp_ticket;
    }

    protected function getCookie(Request $request, string $key = ''): string
    {
        return $request->cookie($key) ?? '';
    }

    protected function setCookie(string $key, string $value, Response $response)
    {
        return \cookie($key, $value, 60, '/', 'laravel-cas.com', true, false, false, 'none');
//        setcookie($key, $value, time() + 3600, '/', 'laravel-cas.com', true, false);
    }

    protected function delCookie(string $key)
    {
        return \cookie($key, '', -1, '/', 'laravel-cas.com', true, false, false, 'none');
    }


}
