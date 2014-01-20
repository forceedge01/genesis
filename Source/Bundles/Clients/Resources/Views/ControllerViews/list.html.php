<div class='wrapper'>
    <div class=''>
        <a href='<?=$this->setRoute('Clients_Create')?>'>Create new Client</a>
    </div>
    <h3>List of all Clients</h3>
    <div class='widget'>
        <?=$this->htmlgen->Output($table, 'table')?>
    </div>
</div>