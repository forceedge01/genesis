<?php if(isset($this->User) && $this->User->Role == 'admin'): ?>

    <?=$this->RenderView(':_Private/MenuItems.html.php')?>

<?php elseif(!empty($this->User->Role)): ?>

    <?=$this->RenderView(':_Protected/MenuItems.html.php')?>

<?php endif; ?>
    </body>
</html>
