<?php

namespace Application\Console\Lib;



class ComponentsUI extends ComponentsAPI{

    
    public function Create()
    {
        // Get Component name from user
        $this->componentName = $this->readUser('Enter name of the component you want to build (Unique): ');

        if($this->CreateComponent())
        {
            echo $this->green ('Component Created Successfully.');
        }

        echo $this->linebreak();
    }

    public function Delete()
    {
        $components = $this->ReadComponents();

        echo $this->linebreak();
        echo $this->blue('List of components in your application: ');
        echo $this->linebreak();
        $this->ShowFormattedArray($components, 1);
        echo $this->linebreak();

        $componentIndex = $this->readUser('Enter component number you want to delete: ');
        $this->componentName = $components[$componentIndex-1];

        $surity = $this->Choice("Are you sure you want to delete `{$this->componentName}` component?", 'Yes');

        echo $this->linebreak ();

        if($surity)
        {
            if($this->DeleteComponent())
            {
                echo $this->green ("Component {$this->componentName} Deleted Successfully.");
            }
            else
            {
                echo $this->red("Unable to delete {$this->componentName} component.");
            }

            echo $this->linebreak(2);
        }
    }
}