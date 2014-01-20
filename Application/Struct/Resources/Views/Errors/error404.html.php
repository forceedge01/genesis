<?=$this->IncludeView('::Header.html.php');?>

<div class="wrapper">

    <h1>Error 404: The page you're looking for could not be found, go to <a href='<?= $this->setRoute(\Get::Config('Application.HomeRoute'))?>'>home page</a></h1>

    <?= $this->setAsset(':Images/Backgrounds/dead-404.jpg') ?>

</div>

<?=$this->IncludeView('::Footer.html.php');?>