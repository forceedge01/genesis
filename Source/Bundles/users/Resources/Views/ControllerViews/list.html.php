<div class="wrapper">

    Welcome <?=$this->User->email;?>, <a href="<?=$this->setRoute('users_logout')?>">Logout</a>

    <div id="wd" class="widget" >

        <?=$this->htmlgen->Output($table, 'table')?>
        
        <input type="text" value="abc" id="xxx" name="username">

    </div>

</div>