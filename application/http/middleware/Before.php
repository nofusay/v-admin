<?php

namespace app\http\middleware;

class Before
{
    public function handle($request, \Closure $next)
    {
        // 添加中间件执行代码
    	// echo 'before<br>';
        return $next($request);
    }
}
