<div class="errorPage">

    <h1>Class::<span><?=$params['Error']['Controller']?></span> not found  for method call <?=$params['Error']['Class']?> as defined in Routes ['<span><?=$params['Error']['Route']?></span>'], Line: <?=$params['Error']['line']?></h1>

    <p>Error Originated from:</p>

    <?php $this->pre($params['Error']['Backtrace'][0]);?>


    <p>Full Backtrace:</p>

    <?php $this->pre($params['Error']['Backtrace']);?>

</div>