<?php

namespace Application\Console;



class TemplateTestCase extends BaseTestingRoutine{

    private function GetTemplatePath($template)
    {

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

    private function CheckForMatches($regex, $contents)
    {
        $matches = null;

        foreach($contents as $column => $content)
        {
            if(preg_match($regex, $content, $matches, PREG_OFFSET_CAPTURE))
            {
                $matches[0]['line'] = $column+1;
                $match[] = $matches;
            }
        }

        if($match)
            return $match;

        return 0;
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

            $reg = array();

            foreach($chunks as $chunk)
            {
                if(strpos($chunk,'[') !== false)
                {
                    $selectorChunks = explode('[', $chunk);
                    $equals = explode('=', $selectorChunks[1]);
                    $equals[1] = substr($equals[1], 0, -1);

                    $reg[] = "{$selectorChunks[0]}[^>]+{$equals[0]}\\s*=\\s*['\"]{$equals[1]}['\"][^>]*";
                }
                else if(strpos($chunk,'#') !== false)
                {
                    $id = explode('#', $chunk);
                    $reg[] = $id[0]."[^<]*id\\s*=\\s*['\"]".$id[1]."['\"][^>]*";
                }
                else if(strpos($chunk,'.') !== false)
                {
                    $class = explode('.', $chunk);
                    $reg[] = $class[0]."[^<]*class\\s*=\\s*['\"]".$class[1]."['\"][^>]*";
                }
            }

            $regex = function($regularExpression) use ($reg){

                foreach($reg as $regex)
                    $regularExpression .= $regex;

                return $regularExpression;
            };

            return "/<{$regex()}>/i";
        }
        else
        {
            if(strpos($selector, '[') !== false)
            {
                $selectorChunks = explode('[', $selector);
                $equals = explode('=', $selectorChunks[1]);
                $equals[1] = substr($equals[1], 0, -1);

                return "/<{$selectorChunks[0]}[^>]+\\s*{$equals[0]}\\s*=\\s*['\"]{$equals[1]}['\"][^>]*>/i";
            }
            else if(strpos($selector,'#') !== false)
            {
                $selectorChunks = explode('#', $selector);
                return "/<{$selectorChunks[0]}[^>]+id\\s*=\\s*['\"]{$selectorChunks[1]}['\"][^>]*>/i";
            }
            else if(strpos($selector, '.') !== false)
            {
                $selectorChunks = explode('.', $selector);
                return "/<{$selectorChunks[0]}[^>]+class\\s*=\\s*['\"]{$selectorChunks[1]}['\"][^>]*>/i";
            }
        }

        return false;
    }

    protected function AssertTemplate($template, $selector = null)
    {
        $passed = $this->green(__FUNCTION__ . '(); Test on '. $template.' passed');
        $failed = $this->red(__FUNCTION__ . '(); Test on '. $template. ($selector ? ' containing '.$selector : '').' failed in class '. get_called_class());

        $cssSelector = $selector;

        $templatePath = $this->GetTemplatePath($template);

        if(is_file($templatePath))
        {
            $contents = file($templatePath);

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
                        self::RegisterPass ($passed, count($matches).' Match(es) found for selector: '.$cssSelector.', Matches:');

                        $index = 1;
                        foreach($matches as $match)
                        {
                            echo $this->linebreak(1),$this->space(8), '- ',$index,'. ',$match[0][0],' Line: ', $match[0]['line'],', Col: ',$match[0][1];
                            $index ++;
                        }
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

    public function AssertTemplateMultiple($template, array $selectors)
    {
        foreach($selectors as $selector)
        {
            $this->AssertTemplate ($template, $selector);
        }
    }
}