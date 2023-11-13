<?php
// version 2.10
if (!class_exists("WA_Include")) {
	
class WA_Include  {
  public $Start_Dir;
  public $File;
  public $BaseName;
  public $Reset;
  public $Added;
  public $Dir;
  public $Body;
  public $Head;
  public $HTML;
  public $Title;
  public $Above;
  public $Links;
  public $Styles;
  public $DocType;
  public $Content;
  public $MetaTags;
  public $MetaKeywords;
  public $MetaDescription;
  public $MetaContentType;
  public $MetaKeywords_Content;
  public $MetaDescription_Content;
  public $MetaContentType_Content;
  public $RemoveTemplateTags;
  public $EditableRegions;
    
	public function __construct( $fileName) {
	  $this->Start_Dir = getcwd();
	  $this->File = $fileName;
	  $this->BaseName =  basename($fileName);
	  $this->Reset = array();
	  $this->Added = array();
	  $this->CleanName();
	  $this->Dir =  dirname($fileName);
	  $this->Body = "";
	  $this->End = "";
	  $this->Head = "";
	  $this->HTML = "";
	  $this->Title = "";
	  $this->Above = "";
	  $this->Links = array();
	  $this->Styles = array();
	  $this->DocType = "";
	  $this->Content =  "";
	  $this->MetaTags = "";
	  $this->MetaKeywords = "";
	  $this->MetaDescription = "";
	  $this->MetaContentType = "";
	  $this->MetaKeywords_Content = "";
	  $this->MetaDescription_Content = "";
	  $this->MetaContentType_Content = "";
	  $this->RemoveTemplateTags = true;
	  $this->EditableRegions = array();
	  $file_exists = @chdir($this->Dir);
	  if (!$file_exists)  {
	    die("Framework file not found: ".$fileName);
	  }
	  ob_start();
	}
	
	public function ApplyClass($tag,$class) {
		$content =$this->Content;
		$foundTag = preg_match('/<'.$tag.'[ >]/i',$content,$match);
		if (!$foundTag) return;
		$beforeTag = substr($content,0,strpos($content,$match[0]));
		$afterTag = substr($content,strlen($beforeTag));
		if (strpos($afterTag,">") === false) return;
		$tagStr = substr($afterTag,0,strpos($afterTag,">")+1);
		$afterTag = substr($afterTag,strlen($tagStr));
		$foundClass = preg_match('/class\s*=\s*[\'"]/i',$tagStr,$match2);
		if ($foundClass) {
			$tagStr = str_replace($match2[0],$match2[0] . $class . " ",$tagStr);
		} else {
			$tagStr = substr($tagStr,0,strlen($tag)+1) . ' class="' . $class . '"' . substr($tagStr,strlen($tag)+1);
		}
	}
	
	public function AppendToEditableRegion($region, $endContent, $prepend=false, $debug = false) {
		$content =$this->Content;
		$toReplace = $this->GetEditableRegion($region, $all=true);
		if (!$toReplace) return;
		$endContent = $this->UpdatePaths($endContent,$this->Start_Dir,$this->Dir);
    if ($prepend) {
		  $this->Content = str_replace($toReplace, $endContent . $toReplace, $content);
		} else {
		  $this->Content = str_replace($toReplace, $toReplace . $endContent, $content);
		}
		if ($debug && $_SERVER['REMOTE_ADDR'] == "76.176.134.27") {
			 die($toReplace . $endContent);
		}
	}
	
	private function CleanName()  {
	  if (strpos($this->BaseName,"?")!==false)  {
		  $QueryString = substr($this->BaseName,strpos($this->BaseName,"?")+1);
		  $this->BaseName =  substr($this->BaseName,0,strpos($this->BaseName,"?"));
		  $QueryArray = explode("&",$QueryString);
		  for ($x=0; $x<sizeof($QueryArray); $x++)   {
			  $QueryVals = explode("=",$QueryArray[$x]);
			  if (sizeof($QueryVals)==2)  {
				  $isArray = false;
				  if(strpos($QueryVals[0],"[]") !== false && strrpos($QueryVals[0],"[]") == (strlen($QueryVals[0])-2)) {
					 $isArray = true;
					 $QueryVals[0] = substr($QueryVals[0], 0, strlen($QueryVals[0])-2);
				  }
				  $QueryVals[0] = urldecode($QueryVals[0]);
				  $QueryVals[1] = urldecode($QueryVals[1]);
				  if ($isArray)  {
					if (!isset($this->Reset[$QueryVals[0]]))  {
						if (isset($_GET[$QueryVals[0]]) && !isset($this->Added[$QueryVals[0]]))  {
						  $this->Reset[$QueryVals[0]] = $_GET[$QueryVals[0]];
						}
						$_GET[$QueryVals[0]] = array();
				          $this->Added[$QueryVals[0]] = true;
					} 
				    $_GET[$QueryVals[0]][] = $QueryVals[1];
				  }  else  {
				    if (isset($_GET[$QueryVals[0]]))  {
					  $this->Reset[$QueryVals[0]] = $_GET[$QueryVals[0]];
				    }
				    $_GET[$QueryVals[0]] = $QueryVals[1];
				  }
				  $this->Added[$QueryVals[0]] = "test";
			  }
		  }
	  }
	}
	
	public function GetEditableRegions() {
		$regions = array();
		$open = "<!-- " .'(Instance|Template)BeginEditable name="([^"]*)" -->';
		$content =$this->Content;
		$editable = preg_match("/".$open."/i",$content,$Name);
		$close =  "<!-- " . '(Instance|Template)EndEditable -->';
		$closeeditable = preg_match("/".$close."/i",$content,$End);
		while ($editable && $closeeditable)  {
		  $beforeOrig = substr($content,0,strpos($content,$Name[0]));
		  $afterOrig = substr($content,strlen($beforeOrig));
		  $regionContent = substr($afterOrig,0,strpos($afterOrig,$End[0])).$End[0];
		  $content = substr($afterOrig,strpos($afterOrig,$End[0])+strlen($End[0]));
		  $regions[$Name[2]] =$regionContent;
		  $editable = preg_match("/".$open."/i",$content,$Name);
		}
		$this->EditableRegions = $regions;
	}
	
	public function ReplaceTemplateRegions($ContentObj) {
	  $this->GetEditableRegions();
	  if (is_array($ContentObj))  {
      $combined = array();
      foreach ($this->EditableRegions as $key => $value) {
        $combined[$key] = array();
        for ($x=0; $x<sizeof($ContentObj); $x++)  {
          if ( preg_replace("/^<[^\>]*>\s*<[^\>]*>/", "", $ContentObj[$x]->GetEditableRegion($key)) != "")  {
            $combined[$key][] = preg_replace("/^<[^\>]*>\s*<[^\>]*>/", "", $ContentObj[$x]->GetEditableRegion($key));
          }
        }
      }
      foreach ($this->EditableRegions as $key => $value) {
        if (implode($combined[$key],"\n") != "")  {
          if ($this->RemoveTemplateTags) {
            $this->Content = str_replace($value,implode($combined[$key],"\n"),$this->Content);
          } else {
            $this->Content = str_replace($value,'<!-- ' . 'InstanceBeginEditable name="'. $key .'" -->'. implode($combined[$key],"\n") . "<!-- " . 'InstanceEndEditable -->',$this->Content);
          }
        }
      }
	  } else {
      foreach ($this->EditableRegions as $key => $value) {
				$repCont = $ContentObj->GetEditableRegion($key);
        if ( preg_replace("/^<[^\>]*>\s*<[^\>]*>/", "", $repCont) != "")  {
          $this->Content = str_replace($value,$repCont,$this->Content);
        } else if ($this->RemoveTemplateTags) {
					$repCont = $value;
					$repCont = substr($repCont, strpos($repCont," -->")+4);
					$repCont = substr($repCont, 0, strrpos($repCont,"<!-- "));
					$this->Content = str_replace($value,$repCont,$this->Content);
				}
      } 
	  }
	}
	
	public function GetEditableRegion($region, $all=false) {
		$openInstance =  "<!-- " . 'InstanceBeginEditable name="'. $region .'" -->';
		$openTemplate =  "<!-- " . 'TemplateBeginEditable name="'. $region .'" -->';
		$content =$this->Content;
		$closeInstance =  "<!-- " . 'InstanceEndEditable -->';
		$closeTemplate =  "<!-- " . 'TemplateEndEditable -->';
		$regionContent = "";
		$closeFound = false;
		$isTemplate =false;
		$openFound = stripos($content,$openInstance);
		if ($openFound !== false) {
			$closeFound = stripos($content,$closeInstance);
		} else {
		  $openFound = stripos($content,$openTemplate);
	      if ($openFound !== false) {
			$closeFound = stripos($content,$closeTemplate);
			$isTemplate = true;
		  }
		}
		
		if ($closeFound !== false)  {
		  $beforeOrig = substr($content,0,$openFound);
		  $afterOrig = substr($content,strlen($beforeOrig));
		  $regionContent = substr($afterOrig,0,stripos($afterOrig,($isTemplate?$closeTemplate:$closeInstance))).($isTemplate?$closeTemplate:$closeInstance);
		}
		if (!$all) {
		  $regionContent = str_ireplace(($isTemplate?$openTemplate:$openInstance), "", $regionContent);
		  $regionContent = str_ireplace(($isTemplate?$closeTemplate:$closeInstance), "", $regionContent);
		}
		return $regionContent;
	}
	
	public function AppendToHead($appendString) {
	  if (strpos($this->Content,"</head>") && $appendString)
	    $this->Content = substr($this->Content,0,strpos($this->Content,"</head>")) . $appendString . substr($this->Content ,strpos($this->Content,"</head>")) ;
	}
	
    public function UpdatePaths($origStr,$endDir,$startDir)  {
		global $WAFW_addProps;
        if ($startDir==".")  $startDir = $endDir;
        if ($startDir=="..") $startDir = "../";
		$props = array();
		$props[] = 'href';
		$props[] = 'action';
		$props[] = 'src';
		
		if ($WAFW_addProps) $props = array_merge($props,$WAFW_addProps);
		//  @import url("css/common.css");
		$subStr = $origStr;
		$matchpat = '/(url|Image|MM_preloadImages|MM_swapImage|href)(\(\s*|\s*=\s*)([\'"])?([^)\'"\r\n]*)\3(\s*\)|\s*[;\,])/i';
		//echo($origStr);
		preg_match($matchpat,$subStr,$subPats);
		$ignore = '/^(https?%3A%2F%2F|https?:\/\/|mailto:|javascript:|cid:|callto:|wtai:|sms:|market:|geopoint:|ymsgr:|msnim:|gtalk:|skype:|sip:|whatsapp:|geo:|data:|tel:|#|\/)/i';
		$combineDir = rel2abs($startDir,$endDir);
		while (sizeof($subPats) > 0)  {
			if ($subPats[1] == "MM_swapImage")  {
				preg_match('/(MM_swapImage)((?:\(\s*|\s*=\s*)[^\r\n,]*,[^\r\n,]*,)([\'"])?([^)\r\n]*)\3(\s*\)|\s*[;\,])/i',$subStr,$subPats);
			}
			if ($subPats[1] == "MM_preloadImages")  {
				preg_match('/MM_preloadImages\(((?:[^\),],?){1,})\)/i',$subStr,$subPats2);
				$pieces = explode(",",$subPats2[1]);
				$subPats[0] = $subPats2[0];
				$subPats[1] = substr($subPats2[0],0,strrpos($subPats2[0],$pieces[sizeof($pieces)-1]));
				$subPats[2] = "";
				$subPats[5] = ")";
				for ($x=0; $x<sizeof($pieces); $x++)  {
				  preg_match('/\s*([\'"])?([^\)\r\n]*)\1/',$pieces[$x],$linkAtts);
				  $subPats[3] = $linkAtts[1];
				  $subPats[4] = $linkAtts[2];
			      if (preg_match($ignore,$linkAtts[2])==0 && $linkAtts[2] != "" && $x<sizeof($pieces)-1) {
			        $absDir = rel2abs($linkAtts[2],$combineDir);
			        $relDir = abs2rel($absDir,$endDir); 
			        $subPats[1] = str_replace($pieces[$x],$subPats[3] . $relDir. $subPats[3] ,$subPats[1]);
		          }
				}
			}
			if (preg_match($ignore,$subPats[4])==0 && $subPats[4] != "") {
			  $absDir = rel2abs($subPats[4],$combineDir);
			  $relDir = abs2rel($absDir,$endDir); 
			  $origStr = str_replace($subPats[0],$subPats[1]. $subPats[2] . $subPats[3] . $relDir. $subPats[3] . $subPats[5],$origStr);
		    }
			$subStr = str_replace($subPats[0],'',$subStr);
		    preg_match($matchpat,$subStr,$subPats);
		}
		$ignoreTags["fb:like"] = array();
		$ignoreTags["fb:like"][] = "action";
		for ($x=0; $x<sizeof($props); $x++)  {
		  $beforeMatch = "";
		  $afterMatch = $origStr;
		  $matchptn = $props[$x];
		  preg_match('/ '.$matchptn.'=(["])([^"\r\n]*)["]/i',$origStr,$subPats);
		  while (sizeof($subPats) > 0)  {
        $beforeMatch .= substr($afterMatch,0,strpos($afterMatch,$subPats[0]));
        $afterMatch = substr($afterMatch,strpos($afterMatch,$subPats[0]) + strlen($subPats[0]));
        $skipIt = false;
        $tagMatch = substr($beforeMatch,strpos($beforeMatch,"<")+1);
        $tagMatch = substr($tagMatch,0,strpos($tagMatch,">"));
        if (strpos($tagMatch," ") !== false) $tagMatch = substr($tagMatch,0,strpos($tagMatch," "));
        if (($matchptn == "action" && $subPats[2]==="") || (isset($ignoreTags[$tagMatch]) && in_array($matchptn,$ignoreTags[$tagMatch]))) {
          $skipIt = true;
        }
        if (preg_match($ignore,$subPats[2])==0 && !$skipIt) {
            $absDir = rel2abs($subPats[2],$combineDir);
            $relDir = abs2rel($absDir,$endDir);
          $beforeMatch .= " " . $matchptn.'=' . $subPats[1] . $relDir. $subPats[1];
        } else {
          $beforeMatch .= $subPats[0];
        }
        preg_match('/ '.$matchptn.'=(["])([^"\r\n]*)["]/i',$afterMatch,$subPats);
		  }
		  $origStr = $beforeMatch . $afterMatch;
      $beforeMatch = "";
      $afterMatch = $origStr;
		  preg_match('/ '.$matchptn.'=([\'])([^\'\r\n]*)[\']/i',$origStr,$subPats);
		  while (sizeof($subPats) > 0)  {
        $beforeMatch .= substr($afterMatch,0,strpos($afterMatch,$subPats[0]));
        $afterMatch = substr($afterMatch,strpos($afterMatch,$subPats[0]) + strlen($subPats[0]));
        $skipIt = false;
        $tagMatch = substr($beforeMatch,strpos($beforeMatch,"<")+1);
        $tagMatch = substr($tagMatch,0,strpos($tagMatch,">"));
        if (strpos($tagMatch," ") !== false) $tagMatch = substr($tagMatch,0,strpos($tagMatch," "));
        if (isset($ignoreTags[$tagMatch]) && in_array($matchptn,$ignoreTags[$tagMatch])) {
          $skipIt = true;
        }
        if (preg_match($ignore,$subPats[2])==0 && !$skipIt) {
            $absDir = rel2abs($subPats[2],$combineDir);
            $relDir = abs2rel($absDir,$endDir);
          $beforeMatch .= " " . $matchptn.'=' . $subPats[1] . $relDir. $subPats[1];
        } else {
          $beforeMatch .= $subPats[0];
        }
        preg_match('/ '.$matchptn.'=([\'])([^\'\r\n]*)[\']/i',$afterMatch,$subPats);
		  }
		  $origStr = $beforeMatch . $afterMatch;
		}
		return $origStr;
	}
	
	public function TagContents($tag,$instr)  {
	  $retVal = $instr;
	  $tagFound = preg_match('/<'.$tag.'[^>]*>/i',$instr, $startTag);
	  $endTagFound = preg_match('/<\/'.$tag.'>/i',$instr, $endTag);
	  if ($tagFound && $endTagFound)  {
	    $retVal = substr($instr, strpos($instr,$startTag[0]) + strlen($startTag[0]));
	    $retVal = substr($retVal,0, strpos($retVal,$endTag[0]));
	  }
	  
	  // if the content hasn't changed, this indiates the tag was not found, then return empty value
	  if($retVal == $instr && $tag != 'body'){
	    $retVal = '';
	  }
	  return $retVal;
	}
	
	public function Initialize($doUpdates=false,$removeEditableRegions=true) {
	  $content = ob_get_clean();
	  chdir($this->Start_Dir);
	  foreach ($this->Added as $key => $value) {
			unset($_GET[$key]);
	  }
	  foreach ($this->Reset as $key => $value) {
			$_GET[$key] = $value;
	  }
	  // update paths
	  if ($doUpdates) $content = $this->UpdatePaths($content,$this->Start_Dir,$this->Dir);
	  
	  $this->Content = $content;
	  preg_match("/<!DOCTYPE[^>]*>\s*/",$content,$doctypes);
	  $doctype = "";
	  if ($doctypes)  {
	    $doctype = $doctypes[0];
	  }
	  
	  $above = $content;
	  
	  $html = $this->TagContents("html",$content);
	  
	  if ($html!="")  {
	    $above = substr($content, 0, strpos($content,$html));
	    if ($doctype) $above = str_replace($doctype,"",$above);
	  }
	  
	  $templateOpen = "/<!-- " .'(Instance|Template)BeginEditable name="([^"]*)" -->/';
	  $templateClose =  "/<!-- " . '(Instance|Template)EndEditable -->/';
	  
	  $body = $this->TagContents("body",$content);
	  $head = $this->TagContents("head",$content);
	   
	  $title = $this->TagContents("title",$content);
	  $title = preg_replace("/^[\w\W]*<title[^>]*>/i", "", $title);
	  $title = preg_replace("/<\/title>[\w\W]*$/i", "", $title);
	  $head = preg_replace("/<title[^>]*>[\w\W]*?<\/title>/i", "", $head);
	  if ($removeEditableRegions) {
        $head = preg_replace($templateOpen, "", $head);
        $head = preg_replace($templateClose, "", $head);
        $body = preg_replace($templateOpen, "", $body);
        $body = preg_replace($templateClose, "", $body);
	  }
	  
	  $this->Body = $body;
		
	  if (strpos($this->Body,"<!-- end -->")) {
	    $this->End = substr($this->Body, strpos($this->Body,"<!-- end -->"));
	    $this->Body = substr($this->Body, 0, strpos($this->Body,"<!-- end -->"));
	  }
		
	  $this->Head = $head;
	  $this->HTML = $html;
	  $this->Title = $title;
	  $this->Above = $above;
	  $this->DocType = $doctype;
	  
	  preg_match_all("/<meta[^>]*>[\n\r]*/i", $head, $metas);
      foreach ($metas as  $meta) {
		if (isset($meta[0]))  {
          $this->MetaTags.= $meta[0] . "\n";
		  if (preg_match('/\s{1,}name\s*=\s*"keywords"/i',$meta[0]))  {
			$this->MetaKeywords = $meta[0];
			if (preg_match("/\scontent\s*=(['".'"'."])([^\r\n]*)\\1/i",$meta[0],$contents))  {
				$this->MetaKeywords_Content = $contents[2];
			}
		  }
		  if (preg_match('/\s{1,}name\s*=\s*"description"/i',$meta[0]))  {
			$this->MetaDescription = $meta[0];
			if (preg_match("/\scontent\s*=(['".'"'."])([^\r\n]*)\\1/i",$meta[0],$contents))  {
				$this->MetaDescription_Content = $contents[2];
			}
		  }
		  if (preg_match('/\s{1,}http-equiv\s*=\s*"Content-Type"/i',$meta[0]))  {
			$this->MetaContentType = $meta[0];
			if (preg_match("/\scontent\s*=(['".'"'."])([^\r\n]*)\\1/i",$meta[0],$contents))  {
				$this->MetaContentType_Content = $contents[2];
			}
		  }
		}
      }
	  preg_match_all("/<link[^>]*>[\n\r]*/i", $head, $links);
      foreach ($links[0] as  $link) {
        if ($link) $this->Links[] = $link. "\n";
      }
      preg_match_all("/(\<\!--\[if [\w\W]*\]\>[\n\r\s\t]*)?<style[^>]*>[\w\W]*?<\/style>[\n\r]*([\n\r\s\t]*\<\!\[endif\]--\>[\n\r\s\t]*)?/i", $head, $styles);
      foreach ($styles[0] as $style) {
        if ($style) $this->Styles[] =$style . "\n";
      }
    }
}
	
}
?>