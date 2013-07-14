<?=$this->IncludeHeader();?>

<div class="wrapper">
    <h3>Hello, please login</h3>
    <form class="form" method="post" action="<?=$this->Path('users_login_auth')?>">
        <div class="formRow"> <label>username:</label> <div class="formRight"><input type="text" name="username"></div></div>
        <div class="formRow"> <label>password:</label> <div class="formRight"><input type="password" name="password"></div></div>
        <div class="formButtons"> <input type="submit" name="login" value="login"></div>
    </form>
</div>

<?=$this->IncludeFooter();?>