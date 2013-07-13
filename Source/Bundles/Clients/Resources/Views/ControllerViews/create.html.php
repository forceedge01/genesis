<div class='wrapper'>
    <div class=''>
        <a href='<?=$this->setRoute('Clients_List')?>'>View All Clients</a>
    </div>
    <h3>Create new Client</h3>
    <div class='widget'>
        <?=$this->htmlgen->Output($form, 'form')?>
    </div>
</div>