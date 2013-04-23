<div class="errorPage">

    <h1>Bundle:: <span><?=$bundle?></span> <?=$message?></h1>

    <p>Error Originated from:</p>

    <pre><?php print_r($params['Backtrace'][0]);?></pre>


    <p>Full Backtrace:</p>

    <pre> <?php print_r($params['Backtrace']);?> </pre>

</div>