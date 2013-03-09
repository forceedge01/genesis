<div id="menuList">
    
    <ul>
        
        <?php if($this->User->Role == 'admin'): ?>
        
            <li><a href="<?=$this->setRoute('Users')?>">Users</a></li>
        
        <?php endif; ?>
            
        <li><a href="<?=$this->setRoute('Site')?>">Sites</a></li>
        
        <li><a href="<?=$this->setRoute('User_Settings')?>">Settings</a></li>
        
    </ul>
    
</div>

<input type="hidden" value="<?=$this->User->Role?>" id="CurrentUserRole">

</div>
