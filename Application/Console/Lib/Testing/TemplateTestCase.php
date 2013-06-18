<?php

namespace Application\Console;



class TemplateTestCase extends BaseTestingRoutine{
    
    private function GetTemplatePath($template){

        $templateParams = explode(':', $template);

        if ($templateParams[0] != null) 
        {
            return str_replace('//', '/', BUNDLES_FOLDER . $templateParams[0] . BUNDLE_VIEWS .'ControllerViews/' . $templateParams[1]);
        }
        else
        {
            return str_replace('//', '/', TEMPLATES_FOLDER . $templateParams[1] );
        }
    }
    
    private function CheckForMatches($regex, $content)
    {
        $matches = null;
        
        if(preg_match($regex, $content, $matches))
        {
            return count($matches);
        }
        else
        {
            return 0;
        }
    }
    
    private function ChunkAndMatchSelector($selector, $content)
    {        
        $regex = $this->GenerateRegex($selector);
        
        return $this->CheckForMatches($regex, $content);
        
    }
    
    private function GenerateRegex($selector)
    {        
        if(strpos($selector, '|'))
        {
            $chunks = explode('|', $selector);
            
            foreach($chunks as $chunk)
            {
                if(strpos($chunk,'['))
                {
                    $selectorChunks = explode('[', $chunk);
                    $equals = explode('=', $selectorChunks[1]);
                    $equals[1] = substr($equals[1], 0, -1);
                
                    $sel = "{$selectorChunks[0]}[^>]+{$equals[0]}\\s*=\\s*['\"]{$equals[1]}['\"][^>]*";
                }
                else if(strpos($chunk,'#'))
                {
                    $id = explode('#', $chunk);
                    $id = '[^<]*'.$id[0]."\\s*=\\s*['\"]".$id[1]."['\"][^>]*";
                }
                else if(strpos($chunk,'.'))
                {
                    $class = explode('.', $chunk);
                    $class = '[^<]*'.$class[0]."\\s*=\\s*['\"]".$class[1]."['\"][^>]*";
                }
            }
            
            return "/<{$sel}{$id}{$class}>/i";
        }
        else
        {
            if(strpos($selector, '['))
            {
                $selectorChunks = explode('[', $selector);
                $equals = explode('=', $selectorChunks[1]);
                $equals[1] = substr($equals[1], 0, -1);
                
                return "/<{$selectorChunks[0]}[^>]+\\s*{$equals[0]}\\s*=\\s*['\"]{$equals[1]}['\"][^>]*>/i";
            }
            else if(strpos($selector,'#'))
            {
                $selectorChunks = explode('#', $selector);
                return "/<{$selectorChunks[0]}[^>]+id\\s*=\\s*['\"]{$selectorChunks[1]}['\"][^>]*>/i";
            }
            else if(strpos($selector, '.'))
            {
                $selectorChunks = explode('#', $selector);
                return "/<{$selectorChunks[0]}[^>]+class\\s*=\\s*['\"]{$selectorChunks[1]}['\"][^>]*>/i";
            }
        }

        return false;
    }
    
    protected function AssertTemplate($template, $selector = null){
        
        $passed = $this->green(__FUNCTION__ . '(); Test on '. $template.' passed');
        $failed = $this->red(__FUNCTION__ . '(); Test on '. $template. ($selector ? ' containing '.$selector : '').' failed in class '. get_called_class());
        
        $cssSelector = $selector;
        
        self::RegisterAssertion();
        
        $templatePath = $this->GetTemplatePath($template);
        
        if(is_file($templatePath))
        {
            $contents = file_get_contents($templatePath);

            if($contents)
            {
                if(!$selector)
                {
                    self::RegisterPass($passed, 'File was found');
                }
                else
                {
                    $matches = $this->ChunkAndMatchSelector($selector, $contents);

                    if($matches)
                    {
                        self::RegisterPass ($passed, count($matches).' Match(es) found for selector: '.$cssSelector);
                    }
                    else
                    {
                        self::RegisterFail ($failed, 'No matches found for selector '.$cssSelector);
                    }
                }
            }
            else
            {
                self::RegisterFail ($failed, 'Error: File is empty');
            }
        }
        else
        {
            self::RegisterFail ($failed, 'Error: File not found');
        }
    }
}