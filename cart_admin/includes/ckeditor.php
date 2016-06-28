<?php
/**
 * @package admin
 * @copyright Copyright 2009-2014 Zen Cart Development Team
 * @copyright Portions Copyright 2010 Kuroi Web Design
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: Original: ckeditor.php 277 2010-05-22 14:09:32Z kuroi $
 */
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}



$var = zen_get_languages();
$jsLanguageLookupArray = "var lang = new Array;\n";
foreach ($var as $key)
{
  $jsLanguageLookupArray .= "  lang[" . $key['id'] . "] = '" . $key['code'] . "';\n";
}
?>
<script type="text/javascript" src="//www.google.com/jsapi"></script>
<script type="text/javascript">if (typeof jQuery == 'undefined') google.load("jquery", "1");</script>
<script type="text/javascript" src="../<?php echo DIR_WS_EDITORS ?>ckeditor/ckeditor.js"></script>
<script type="text/javascript"><!--
$(document).ready(function() {
  <?php echo $jsLanguageLookupArray ?>
  $('textarea').each(function()	{
    if ($(this).attr('class') == 'editorHook' || ($(this).attr('name') != 'message' && $(this).attr('class') != 'noEditor'))
    {
      index = $(this).attr('name').match(/\d+/);
      if (index == null) index = <?php echo $_SESSION['languages_id'] ?>;
      CKEDITOR.replace($(this).attr('name'),
        {
          coreStyles_underline : { element : 'u' },
          width : 760,
          language: lang[index],
          allowedContent : true, 
        /*  filebrowserBrowseUrl: '/browser/browse.php?type=Images', */
          filebrowserUploadUrl: '/editors/ckeditor/plugins/simpleupload/simpleupload.php?type=Files'
        });
    }
  });
});
//--></script>
