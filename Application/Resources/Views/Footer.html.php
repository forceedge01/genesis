<?php if(isset($this->User) && $this->User->Role == 'admin'): ?>

    <?=$this->RenderTemplate('Templates::_Private/MenuItems.html.php')?>

<?php elseif(!empty($this->User->Role)): ?>

    <?=$this->RenderTemplate('Templates::_Protected/MenuItems.html.php')?>

<?php else: ?>



<?php endif; ?>
    </body>
</html>
