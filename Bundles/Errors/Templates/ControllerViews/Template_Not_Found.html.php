<div class="errorPage">

    <h1>Template:: <span><?=$template?></span> <?=$message?></h1>

    <p>Error Originated from:</p>

    <pre><?php print_r($params['Backtrace'][1]);?></pre>


    <p>Full Backtrace:</p>

    <pre> <?php print_r($params['Backtrace']);?> </pre>

</div>