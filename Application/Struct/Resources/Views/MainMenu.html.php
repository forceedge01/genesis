<?php if(($this->User->email)): ?>
    <div id="header">
        <div class="floatL">
            <a href="<?=$this->Path('users_List')?>"><img id="headerLogo" src="<?=$this->Asset('Images/Icons/logo-white.png');?>" /></a>
            Welcomes you <?=$this->User->email;?>!
        </div>
        <div class="floatR menuItem"><a href="<?=$this->Path('users_logout')?>">Log Out</a></div>
        <div id="Menu" class="menuItem floatR">Menu</div>
    </div>
<?php endif;?>