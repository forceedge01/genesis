<?=$this->IncludeView(':Header.html.php');?>

<div class="errorPage">

    <h4>Bundle:: <span>'<?=$bundle?>'</span> <?=$message?></h4>

    <p>Error Originated from:</p>

    <pre><?php print_r($params['Backtrace'][0]);?></pre>


    <p>Full Backtrace:</p>

    <pre> <?php print_r($params['Backtrace']);?> </pre>

</div>