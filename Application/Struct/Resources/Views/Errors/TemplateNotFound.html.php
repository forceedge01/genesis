<?=$this->IncludeView('::Header.html.php');?>

<div class="errorPage">

    <h1>Template:: <span><?=$Template?></span> <?=$message?> Not Found</h1>

    <p>Error Originated from:</p>

    <pre><?php print_r($params['Backtrace'][1]);?></pre>


    <p>Full Backtrace:</p>

    <pre> <?php print_r($params['Backtrace']);?> </pre>

</div>