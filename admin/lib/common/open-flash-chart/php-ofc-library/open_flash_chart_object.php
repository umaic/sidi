<?php

function open_flash_chart_object_str( $width, $height, $url, $use_swfobject=true, $base='' )
{
    //
    // return the HTML as a string
    //
    return _ofc( $width, $height, $url, $use_swfobject, $base );
}

function open_flash_chart_object( $width, $height, $url, $use_swfobject=true, $base='' )
{
    //
    // stream the HTML into the page
    //
    echo _ofc( $width, $height, $url, $use_swfobject, $base );
}

function _ofc( $width, $height, $url, $use_swfobject, $base )
{
    //
    // I think we may use swfobject for all browsers,
    // not JUST for IE...
    //
    //$ie = strstr(getenv('HTTP_USER_AGENT'), 'MSIE');
    
    //
    // escape the & and stuff:
    //
    $url = urlencode($url);
    
    //
    // output buffer
    //
    $out = array();
    
    //
    // check for http or https:
    //
    if (isset ($_SERVER['HTTPS']))
    {
        if (strtoupper ($_SERVER['HTTPS']) == 'ON')
        {
            $protocol = 'https';
        }
        else
        {
            $protocol = 'http';
        }
    }
    else
    {
        $protocol = 'http';
    }
    
    //
    // if there are more than one charts on the
    // page, give each a different ID
    //
    global $open_flash_chart_seqno;
    $obj_id = 'chart';
    $div_name = 'flashcontent';
    
    //$out[] = '<script type="text/javascript" src="'. $base .'js/ofc.js"></script>';
    
    if( !isset( $open_flash_chart_seqno ) )
    {
        $open_flash_chart_seqno = 1;
        $out[] = '<script type="text/javascript" src="'. $base .'js/swfobject.js"></script>';
    }
    else
    {
        $open_flash_chart_seqno++;
        $obj_id .= '_'. $open_flash_chart_seqno;
        $div_name .= '_'. $open_flash_chart_seqno;
    }
    
    if( $use_swfobject )
    {
	// Using library for auto-enabling Flash object on IE, disabled-Javascript proof  
    $out[] = '<div id="'. $div_name .'"></div>';
	$out[] = '<script type="text/javascript">';
	$nocache = time();
	$out[] = 'var so = new SWFObject("'. $base .'pre-open-flash-chart.swf?nocache='.$nocache.'", "'. $obj_id .'", "'. $width . '", "' . $height . '", "9", "#FFFFFF");';
	//$out[] = 'so.addVariable("width", "' . $width . '");';
	//$out[] = 'so.addVariable("height", "' . $height . '");';
	$out[] = 'so.addVariable("data", "'. $url . '");';
	$out[] = 'so.addParam("allowScriptAccess", "sameDomain");';
	$out[] = 'so.write("'. $div_name .'");';
	$out[] = '</script>';
	$out[] = '<noscript>';
    }

    $nocache = time();
    $out[] = '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="' . $protocol . '://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" ';
    $out[] = 'width="' . $width . '" height="' . $height . '" id="ie_'. $obj_id .'" align="middle">';
    $out[] = '<param name="allowScriptAccess" value="sameDomain" />';
    $out[] = '<param name="movie" value="'. $base .'pre-open-flash-chart.swf?nocache='.$nocache.'&width='. $width .'&height='. $height . '&data='. $url .'" />';
//    $out[] = '<param name="movie" value="'. $base .'pre-open-flash-chart.swf" />';
    $out[] = '<param name="quality" value="high" />';
    $out[] = '<param name="bgcolor" value="#FFFFFF" />';
    $out[] = '<embed src="'. $base .'pre-open-flash-chart.swf?nocache='.$nocache.'&data=' . $url .'&width='.$width.'&height='.$height.'" quality="high" bgcolor="#FFFFFF" width="'. $width .'" height="'. $height .'" name="'. $obj_id .'" align="middle" allowScriptAccess="sameDomain" ';
//    $out[] = '<embed src="'. $base .'pre-open-flash-chart.swf" align="middle" allowScriptAccess="sameDomain" ';
    $out[] = 'type="application/x-shockwave-flash" pluginspage="' . $protocol . '://www.macromedia.com/go/getflashplayer" id="'. $obj_id .'"/>';
    $out[] = '</object>';

    if ( $use_swfobject ) {
	$out[] = '</noscript>';
    }
    
    return implode("\n",$out);
}
?>