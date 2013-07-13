<?=$this->IncludeHeader();?>

<div class="wrapper">
    <h3>Hello, please login</h3>
    <form class="form" method="post" action="<?=$this->Path('users_login_auth')?>">
        <div> username: <input type="text" name="username"></div>
        <div> password: <input type="password" name="password"></div>
        <div> <input type="submit" name="login" value="login"></div>
    </form>
</div>

<?=$this->IncludeFooter();?>