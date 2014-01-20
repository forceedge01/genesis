<!DOCTYPE html>
<html>
    <head>
        <title><?=$this->title;?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <?=$this->RenderView(':CSS:basic.block.php')?>
        <?=$this->RenderView(':JS:basic.block.php')?>
    </head>
    <body>
        <!-- sample comment goes here -->
        <?=$this->RenderView('::MainMenu.html.php')?>
        <div class="wrapper">
            <?=$this->FlashAll();?>
            <div id="JSEvent"></div>
        </div>