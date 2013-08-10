<div class="errorPage">

    <h1>Class::<span><?=$Error['Controller']?></span> not found  for method call <?=$Error['Class']?> as defined in Routes ['<span><?=$Error['Route']?></span>'], Line: <?=$Error['line']?></h1>

    <p>Error Originated from:</p>

    <?php $this->pre($Error['Backtrace'][0]);?>


    <p>Full Backtrace:</p>

    <?php $this->pre($Error['Backtrace']);?>

</div>