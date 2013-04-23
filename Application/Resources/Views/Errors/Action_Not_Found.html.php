<div class="errorPage">

    <h1>Action:: <span><?=$params['Error']['Action']?></span> not found in Class::<span><?=$params['Error']['Class']?></span> defined as <?=$params['Error']['Controller']?> in Routes (' <span><?=$params['Error']['Route']?></span> ').</h1>

    <p>Error Originated from:</p>

    <?php $this->pre($params['Error']['Backtrace'][0]);?>


    <p>Full Backtrace:</p>

    <?php $this->pre($params['Error']['Backtrace']);?>  

</div>