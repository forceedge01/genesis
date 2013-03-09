<body>

    <div id="header">

        <div class="floatL">
        
            <a href="<?=$this->setRoute('Site')?>"><img id="headerLogo" src="<?=$this->Asset('Images/Icons/logo-white.png');?>" /></a> Welcomes you <?=$this->User->Email;?>!
            
        </div>

        <?php if(SESSION_ENABLED): ?>
        
            <div class="floatR menuItem"><a href="<?=$this->setRoute('Auth_Logout')?>">Log Out</a></div>

            <div id="Menu" class="menuItem floatR">Menu</div>
        
        <?php endif; ?>

    </div>

    <div class="wrapper">

        <?=$this->FlashAll();?>

        <div id="JSEvent"></div>