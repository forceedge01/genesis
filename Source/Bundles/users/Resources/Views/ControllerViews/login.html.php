Hello, please login

<form class="form" method="post" action="<?=$this->setRoute('users_login_auth')?>">

    <div> username: <input type="text" name="username"></div>
    <div> password: <input type="password" name="password"></div>

    <div> <input type="submit" name="login" value="login"></div>
</form>
