<?=$this->Div('wrapper');?>
    <h3>Hello, please login</h3>
    <form class="form" method="post" action="<?=$this->Path('users_login_auth')?>">

        <?=$this->Div('formRow');?>
            <?=$this->Label('Username:', 'username');?>
            <?=$this->Div('formRight');?>
                <?=$this->Widget('username','Enter your username');?>
        <?=$this->EndDiv(2);?>

        <?=$this->Div('formRow');?>
            <?=$this->Label('Password:', 'password');?>
            <?=$this->Div('formRight');?>
                <?=$this->Widget('password', '********', 'password');?>
        <?=$this->EndDiv(2);?>

        <?=$this->Div('formButtons');?>
            <?=$this->Widget('login','Login','submit');?>
        <?=$this->EndDiv();?>

    </form>
<?=$this->EndDiv();?>