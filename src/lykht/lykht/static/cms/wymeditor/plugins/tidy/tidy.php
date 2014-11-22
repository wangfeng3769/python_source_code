<?php

// security warning: read the comment below before removing this line!
die ('this functionality is disabled by default. please see plugins/tidy/tidy.php for additional information on how to setup tidy.');

/*

 warning: enabling this file is not secure!

 generally speaking, it is a bad idea to have a script that outputs what you send it.
 it could easily be used to steal cookies from you, for instance if:
  www.badsite.com has a hidden iframe with src="/path/to/tidy.php?html="+escape("... some javascript that sends your cookies to badsite.org ...")

 you could filter out <script> tags, onclick, onblur, onload, onmouseover... attributes
 if you *really* need this functionality. or, even better, you could allow only the tags and
 attributes that you explicitly allow (future versions of wymeditor might provide this
 functionality by default - please contribute).

 be warned that providing authentication is not enough to guard you agains the attack. when
 someone is authenticated in your page and visits www.badsite.com, the iframe has the proper
 authentication so nothing has changed.

*/

if (get_magic_quotes_gpc()) $html = stripslashes($_request['html']);
else $html = $_request['html'];

if(strlen($html) > 0) {

  // specify configuration
  $config = array(
            'bare'                        => true,
            'clean'                       => true,
            'doctype'                     => 'strict',
            'drop-empty-paras'            => true,
            'drop-font-tags'              => true,
            'drop-proprietary-attributes' => true,
            'enclose-block-text'          => true,
            'indent'                      => false,
            'join-classes'                => true,
            'join-styles'                 => true,
            'logical-emphasis'            => true,
            'output-xhtml'                => true,
            'show-body-only'              => true,
            'wrap'                        => 0);

  // tidy
  $tidy = new tidy;
  $tidy->parsestring($html, $config, 'utf8');
  $tidy->cleanrepair();

  // output
  echo $tidy;
} else {

echo ('0');
}
?>
