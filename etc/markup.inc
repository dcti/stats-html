<?

// This file implements the 'markup_to_html' function that translates
// UBB-style markup codes into HTML that is safe to display.  The
// intention is that any richly-formatted user-modifyable fields be
// made to accept and store UBB-style markup within the database.  As
// those fields are retrieved from the database and displayed back on
// a webpage, this conversion function is used first.
//
// It's recommended that any resulting HTML be displayed within a
// single-cell table for the purposes of preventing run-on tags that
// do not have a closing block (since this translator does not enforce
// tag closure).
//
// This requires at least PHP 4.0.6 due to the use of array_map(),
// and the PREG_SPLIT_DELIM_CAPTURE flag on preg_split().
//
// $Id: markup.inc,v 1.3 2002/01/24 21:11:29 jlawson Exp $


// This is the only function that is meant to be publicly callable.
function markup_to_html($markup) {
  $marr = preg_split('/(\[.*?\])/', $markup, -1, PREG_SPLIT_DELIM_CAPTURE);
  return join('', array_map(private_markup_walker, $marr));
}

// Internal helper function.
function private_markupurl_safety($url) {
  if (strlen($url) > 128) return '';
  $newurl = urldecode($url);
  if (!preg_match('/^(http|https):\/\//i', $newurl)) return '';
  if (preg_match('/[\x00-\x1F\<\>\[\]\(\)\"\']/', $newurl)) return '';
  if (preg_match('/(javascript|jscript|vbscript)/i', $newurl)) return '';

  // looks like a safe URL, so just return the original.
  return $url;
}

// Internal helper function.
function private_markup_walker($element) {
  if (strcasecmp($element, '[B]') == 0) return '<b>';
  elseif (strcasecmp($element, '[/B]') == 0) return '</b>';
  elseif (strcasecmp($element, '[I]') == 0) return '<i>';
  elseif (strcasecmp($element, '[/I]') == 0) return '</i>';
  elseif (strcasecmp($element, '[BR]') == 0) return '<br/>';
  elseif (strcasecmp($element, '[HR]') == 0) return '<hr>';
  elseif (strcasecmp($element, '[CENTER]') == 0) return '<p align="center">';
  elseif (strcasecmp($element, '[/CENTER]') == 0) return '</p>';


  $matches = array();
  if (preg_match('/^\[URL=(.*?)\]$/i', $element, $matches))
    {
      $newurl = private_markupurl_safety($matches[1]);
      if ($newurl != '') return "<a href=\"$newurl\">";
      else return "<br><b><i>Unsafe URL specified</i></b><br>";
    }
  elseif (preg_match('/^\[IMG=(.*?)\]$/i', $element, $matches))
    {
      $newurl = private_markupurl_safety($matches[1]);
      if ($newurl != '') return "<img src=\"$newurl\">";
      else return "<br><b><i>Unsafe URL specified</i></b><br>";
    }

  return htmlspecialchars($element, ENT_NOQUOTES);
}

?>
