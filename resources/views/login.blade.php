<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <title>SSO单点登录</title>
</head>
<body>
<h1>欢迎访问单点登录系统</h1>
<form action="doLogin" method="post">
    @csrf
    <input type="text" name="username" placeholder="请输入用户名"/>
    <input type="password" name="password" placeholder="请输入密码"/>
    <input type="hidden" name="returnUrl" value="{{$returnUrl}}">
    <input type="submit" value="提交登录"/>
</form>
<span style="color:red" th:text="{{$errmsg}}"></span>

</body>
</html>
