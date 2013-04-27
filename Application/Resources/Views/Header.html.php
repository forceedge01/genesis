<!DOCTYPE html>
<html>
    <head>
        <title><?=$this->title;?></title>

        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

        <?=$this->RenderTemplate(':CSS/basic.css.html.php')?>

        <?=$this->RenderTemplate(':JS/basic.js.html.php')?>

    </head>

    <body>

        <?=$this->RenderTemplate(':MainMenu.html.php')?>

        <div class="wrapper">

            <?=$this->FlashAll();?>

            <div id="JSEvent"></div>

        </div>