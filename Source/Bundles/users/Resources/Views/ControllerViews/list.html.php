<?=$this->includeHeader();?>

<div class="wrapper">

    Welcome <?=$this->User->email;?>, <a href="<?=$this->setRoute('users_logout')?>">Logout</a>

    <div id="wd" class="widget" >

        <?=$this->htmlgen->Output($table, 'table')?>

    </div>

</div>

<?=$this->includeFooter();?>