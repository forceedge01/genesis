<div class="wrapper">

    <div class=""><a href="<?=$this->setRoute('people_Create')?>">Create new people</a></div>

    <div class="widget">

        <?=$this->htmlgen->Output($table, 'table')?>
        
        <input type="text" id="abc" name="username">

    </div>

</div>