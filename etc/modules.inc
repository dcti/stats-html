<?php 
// vi: ts=2 sw=2 tw=120 syntax=php
// $Id: modules.inc,v 1.24 2004/04/21 19:18:42 paul Exp $
function plural($num)
{
    if (abs($num) > 1 or $num == 0) {
        return 's';
    } else {
        return '';
    } 
} 

function row_background_color($p_index = null, $color_a = '', $color_b = '')
{
	static $t_index = 1;
		
	if ( null !== $p_index ) {
		$t_index = $p_index;
	}    
    
    if ( 1 == $t_index++ % 2 ) {
		if ($color_a == '') $color_a = $GLOBALS['bar_color_a'];
        return $color_a;
    } else {
		if ($color_b == '') $color_b = $GLOBALS['bar_color_b'];
        return $color_b;
    } 
} 

function safe_display($string)
{
    return stripslashes(htmlspecialchars($string));
} 

function html_rank_arrow($change)
{
    if ($change > 0) {
        return "<span style=\"color: #009900\">(<img src=\"/images/up.gif\" alt=\"+\">$change)</span>";
    } else {
        if ($change < 0) {
            $offset = - $change;
            return "<span style=\"color: #990000\">(<img src=\"/images/down.gif\" alt=\"-\">$offset)</span>";
        } 
    } 
} 

function number_style_convert($number, $decimal_places = 0)
{
    global $number_style;
    if ($number_style == "US") {
        $number = number_format($number, $decimal_places, ".", ",");
    } 
    if ($number_style == "EU") {
        $number = number_format($number, $decimal_places, ".", ".");
    } 
    return $number;
} 

function send_mail($to_address, $from_address, $subject, $message)
{
    $path_to_sendmail = "/usr/sbin/sendmail";
    $fp = popen("$path_to_sendmail -t -f $from_address", "w");
    $num = fputs($fp, "To: $to_address\n");
    $num += fputs($fp, "From: $from_address\n");
    $num += fputs($fp, "X-Errors-To: passmail@distributed.net\n");
    $num += fputs($fp, "X-Distributed: Join the cows!  http://www.distributed.net/ ]:8)\n");
    $num += fputs($fp, "X-Mailer: distributed.net stats password mailer 1.0\n");
    $num += fputs($fp, "Subject: $subject\n\n");
    $num += fputs($fp, "$message");
    pclose($fp);
    if ($num > 0) {
        return 1;
    } else {
        return 0;
    } 
} 

?>