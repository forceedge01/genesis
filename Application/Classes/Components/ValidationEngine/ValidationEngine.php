<?php

namespace Application\Components\ValidationEngine;



use Application\Core\Debugger;

class ValidationEngine extends Debugger{

    private
            $tags;

    public function validateHTML($template){

        $this->htmlValidationTags();

        $this->validateTags($template);

    }

    private function htmlValidationTags(){

        //These tags are if opened should be closed and should be in the document only once.
        $tags['OpenCloseOnceTags'] = array(

            'html',
            'body',
            'head',
            'title'

        );

        //Every one of these tags if opened should be closed. Capital form of tags will be computed automatically.
        $tags['OpenCloseTags'] = array(

            'div',
            'span',
            'textarea',
            'table',
            'tr',
            'td',
            'th',
            'thead',
            'tfoot',
            'tbody',
            'script',
            'fieldset',
            'label',
            'form',
            'h1',
            'h2',
            'h3',
            'h4',
            'h5',
            'h6',
            'p',
            'a',
            'area',
            'b',
            'i',
            'pre',
            'u',
            'em',
            'center',
            'iframe',
            'legend',
            'li',
            'ol',
            'ul',
            'select',
            'option',
            'strike',
            'strong',
            'style',
            'tt'

        );

        //These tags are a must for every HTML rendered template.
        //doctype tag is computed separately and automatically.
        $tags['MustTags'] = array(

            'html',
            'body',
            'title'
        );

        $this->tags = $tags;

        return true;

    }

    private function validateTags($template){

        $syntaxCount = array();

        $doctypePattern = '/<!doctype(((\\s)+.)*)?/is';

        $re1='(<)';	# Any Single Character 1
//        $re2='((?:[a-z][a-z]+))';	# Word 1
        $re3='(\\s*)';	# White Space 1
        $re4='((?:[a-z][a-z]+))';	# Word 2
        $re5='(\\s)*';	# White Space 2
        $re6='(=)';	# Any Single Character 2
        $re7='(\\s)*';	# White Space 3
        $re8='(".*?")';	# Double Quote String 1
        $re9='(\\s)*';	# White Space 4
        $re10='(>)';	# Any Single Character 3

        $beforeOpenTagPattern = '/'.$re1;

        $afterOpenTagPattern = $re3.'('.$re4.$re5.$re6.$re7.$re8.$re9.')*'.$re10.'/si';

        $beforeCloseTagPattern = '/<(\\/)(';

        $afterCloseTagPattern = ')>/is';

        foreach($this->tags['OpenCloseOnceTags'] as $openCloseOnceTag){

            $syntaxCount[$openCloseOnceTag] = preg_match_all($beforeOpenTagPattern . $openCloseOnceTag . $afterOpenTagPattern, $template, $matches);

            $syntaxCount['/'.$openCloseOnceTag] = preg_match_all($beforeCloseTagPattern . $openCloseOnceTag . $afterCloseTagPattern, $template, $matches);

            if($syntaxCount[$openCloseOnceTag] != $syntaxCount['/'.$openCloseOnceTag])
                    $syntaxCount['HTMLERROR'][] = 'Mismatch of &#60;'.$openCloseOnceTag.'> element detected, you have '.$syntaxCount[$openCloseOnceTag].' open tags and '.$syntaxCount['/'.$openCloseOnceTag].' closed tags for this element in your template.';

            else{

                if($syntaxCount[$openCloseOnceTag] != 1)
                        $syntaxCount['HTMLERROR'][] = '&#60;'.$openCloseOnceTag.'> must occur only and atleast once in a html document, '.$syntaxCount[$openCloseOnceTag].' times detected.';

                else if($syntaxCount['/'.$openCloseOnceTag] != 1)
                        $syntaxCount['HTMLERROR'][] = '&#60;/'.$openCloseOnceTag.'> must occur only and atleast once in a html document, '.$syntaxCount['/'.$openCloseOnceTag].' times detected.';
            }

        }

        foreach($this->tags['OpenCloseTags'] as $openCloseTag){

            $syntaxCount['full'.$openCloseTag] = preg_match_all($beforeOpenTagPattern . $openCloseTag . $afterOpenTagPattern, $template, $matches);//counts fully qualified tags

            $syntaxCount['/'.$openCloseTag] = preg_match_all($beforeCloseTagPattern . $openCloseTag . $afterCloseTagPattern, $template, $matches);//counts closing tags

            if($syntaxCount['full'.$openCloseTag] != $syntaxCount['/'.$openCloseTag]){

                $syntaxCount['open'.$openCloseTag] = preg_match_all($beforeOpenTagPattern . $openCloseTag .'((\\s)+(.*?))*'.$re10.'/si', $template, $matches);//counts open tags

                if($syntaxCount['open'.$openCloseTag] == $syntaxCount['/'.$openCloseTag]){

                    $syntaxCount['HTMLERROR'][] = 'You have invalid attributes in '.($syntaxCount['/'.$openCloseTag] - $syntaxCount['full'.$openCloseTag]).' of &#60;'.$openCloseTag.'> element, '.$syntaxCount['open'.$openCloseTag].' open tags and '.$syntaxCount['/'.$openCloseTag].' closed tags, '.$syntaxCount['full'.$openCloseTag].' open tags and '.$syntaxCount['/'.$openCloseTag].' closed tags with correct syntax for this element detected in current template.';

                }
                else{
                    $syntaxCount['HTMLERROR'][] = 'Mismatch of &#60;'.$openCloseTag.'> element, '.$syntaxCount['open'.$openCloseTag].' open tags and '.$syntaxCount['/'.$openCloseTag].' closed tags,  '.$syntaxCount['full'.$openCloseTag].' open tags and '.$syntaxCount['/'.$openCloseTag].' closed tags with correct syntax for this element detected in current template.';

                }
            }
        }

        foreach($this->tags['MustTags'] as $mustTag){

            $syntaxCount[$mustTag] = preg_match_all($beforeOpenTagPattern . $mustTag . $afterOpenTagPattern, $template, $matches);

            if($syntaxCount[$mustTag] != 1)
                $syntaxCount['HTMLERROR'][] = '&#60;'.$mustTag.'> must occur only and atleast once in a html document, '.$syntaxCount[$mustTag].' times detected.';

        }

        $syntaxCount['Doctype'] = preg_match_all($doctypePattern, $template, $matches);

        if($syntaxCount['Doctype'] != 1){
            $syntaxCount['HTMLERROR'][] = 'Every html document should have a valid doctype!';
        }

        if(isset($syntaxCount['HTMLERROR']))
            foreach(@$syntaxCount['HTMLERROR'] as $error){

                echo 'HTML Error: '.$error.'<br /><br />';

            }

        unset($syntaxCount);
    }
}