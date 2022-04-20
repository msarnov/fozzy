<?php

/**
 * PHQ9 form using form api     view.php
 * open a previously completed PHQ9 form for further editing
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Ruth Moulton <moulton ruth@muswell.me.uk>
 * @copyright Copyright (c) 2021 ruth moulton <ruth@muswell.me.uk>
 *
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

require_once("phq9.inc.php");  // common strings, require_once(globals.php), other includes etc

use OpenEMR\Common\Csrf\CsrfUtils;    // security module
use OpenEMR\Core\Header;
use Mpdf\Mpdf;  /* used to generate a pdf of the form */
?>
<html><head>
 <head>
    <title><?php echo text($str_form_title); ?> </title>
    <?php Header::setupHeader(); ?>
</head>
<body class="body_top">
<?php // read in the values from the filled in form held in db
$obj = formFetch("form_phq9", $_GET["id"]); ?>
<script>
// get scores from previous saving of the form
var phq9_score = 0;
</script>
<SCRIPT
  src="<?php echo $rootdir;?>/forms/phq9/phq9_javasrc.js">
 </script>

<SCRIPT>
// stuff that uses embedded php must go here, not in the include javascript file - it must be executed on server side before page is sent to client. included javascript is only executed on the client

function create_q10(question, menue){
 // create the question - the second part is italicised
       var text = document.createTextNode(jsAttr(<?php echo js_escape($str_q10); ?>));
       question.appendChild(text);
       var new_line = document.createElement("br"); // second part is in italics
       var ital = document.createElement("i"); // second part is in italics
       var question_2 = document.createTextNode(jsAttr(<?php echo js_escape($str_q10_2); ?>));
       ital.appendChild(question_2) ;
       question.name = "tenth";
       question.appendChild(new_line);
       question.appendChild(ital);

// populate the   the menue
         menue.options[0] = new Option ( <?php echo js_escape($str_not); ?>, "0");
         menue.options[1] = new Option ( <?php echo js_escape($str_somewhat); ?>, "1");
         menue.options[2] = new Option ( <?php echo js_escape($str_very); ?>, "2");
         menue.options[3] = new Option ( <?php echo js_escape($str_extremely);?>, "3");
         menue.options[4] = new Option ( <?php echo js_escape($str_default);  ?>, "undef");
}
// check user really wants to exit without saving new answers
function nosave_exit() {
var conf = confirm ( <?php echo js_escape($str_nosave_confirm); ?> );
if (conf) {
    window.location.href="<?php echo $GLOBALS['form_exit_url']; ?>";
    }
return ( conf );
}
</script>

<form method=post action="<?php echo $rootdir;?>/forms/phq9/save.php?mode=update&id=<?php echo attr_url($_GET["id"]); ?>" name="my_form" >
<input type="hidden" name="csrf_token_form" value="<?php echo attr(CsrfUtils::collectCsrfToken()); ?>" />
<br></br>
<span   ><font size=4><?php echo text($str_form_name); ?></font></span>
<br></br>
<input type="Submit" value="<?php echo xla('Save Form'); ?>" style="color: #483D8B" >
&nbsp &nbsp
<input type="button" value="<?php echo attr($str_nosave_exit);?>" onclick="top.restoreSession();return( nosave_exit());" style="color: #483D8B">
 <br>
<span class="text"><h2><?php echo xlt('How often have you been bothered by the following over the past 2 weeks?'); ?></h2></span>
<table>
<tr>
<td>
<span class="text"><?php echo xlt('Little interest or pleasure in doing things'); ?></span>
<select name="interest_score" onchange="update_score(0, my_form.interest_score.value);">
     <option value="0"><?php echo text($str_not); ?></option>
    <option value="1"><?php echo text($str_several); ?></option>
    <option value="2"><?php echo text($str_more); ?></option>
    <option value="3"><?php echo text($str_nearly); ?></option>
    </select>
<script>
     // set the default to the previous value - so it is displayed in the menue box
    document.my_form.interest_score.options[<?php echo text($obj['interest_score']); ?>].defaultSelected=true;
    var i = <?php echo text($obj['interest_score']); ?> ; //the value from last time
    phq9_score += i;
    all_scores[0] = i;
</script>
 <br>
</br>
</tr>
 </table>
  <table>
  <tr>
  <td>
<span class="text" ><?php echo xlt('Feeling down, depressed, or hopeless'); ?></span>
<select name="hopeless_score" onchange="update_score(1, my_form.hopeless_score.value);" >
    <option value="0"><?php echo text($str_not); ?></option>
    <option value="1"><?php echo text($str_several); ?></option>
    <option value="2"><?php echo text($str_more); ?></option>
    <option value="3"><?php echo text($str_nearly); ?></option>
 </select>
<script>
     // set the default to the previous value - so it is displayed in the menue box
     var i = <?php echo text($obj['hopeless_score']); ?>; //the value from last time
   document.my_form.hopeless_score.options[i].defaultSelected=true;
    phq9_score += i;
    all_scores[1] = i;
</script>
 <br></br>
</tr>
 </table>
  <table>
  <tr>
  <td>
<span class="text" ><?php echo xlt('Trouble falling or staying asleep, or sleeping too much'); ?></span>
<select name="sleep_score" onchange="update_score(2, my_form.sleep_score.value);" >
    <option value="0"><?php echo text($str_not); ?></option>
    <option value="1"><?php echo text($str_several); ?></option>
    <option value="2"><?php echo text($str_more); ?></option>
    <option value="3"><?php echo text($str_nearly); ?></option>
    </select>
       <script>
     // set the previous value to the default - so it is displayed in the menue box
      var i = <?php echo text($obj['sleep_score']); ?> ; //the value from last time
    document.my_form.sleep_score.options[i].defaultSelected=true;
    phq9_score += i;
    all_scores[2] = i;
    </script>
     <br></br>
</tr>
 </table>
 <table>
 <tr><td>
<span class="text" ><?php echo xlt('Feeling tired or having little energy'); ?></span>
<select name="fatigue_score" onchange="update_score(3, my_form.fatigue_score.value);">
    <option value="0"><?php echo text($str_not); ?></option>
    <option value="1"><?php echo text($str_several); ?></option>
    <option value="2"><?php echo text($str_more); ?></option>
    <option value="3"><?php echo text($str_nearly); ?></option>
 </select>
<script>
     // set the previous value to the default - so it is displayed in the menue box
      var i = <?php echo text($obj['fatigue_score']); ?> ; //the value from last time
    document.my_form.fatigue_score.options[i].defaultSelected=true;
    phq9_score += i;
    all_scores[3] = i;
    </script>
    <br></br>
</tr>
 </table>
  <table>
  <tr><td>
<span class="text" ><?php echo xlt('Poor appetite or overeating'); ?></span>
<select name="appetite_score" onchange="update_score(4, my_form.appetite_score.value);">
    <option value="0"><?php echo text($str_not); ?></option>
    <option value="1"><?php echo text($str_several); ?></option>
    <option value="2"><?php echo text($str_more); ?></option>
    <option value="3"><?php echo text($str_nearly); ?></option>
    </select>
<script>
     // set the previous value to the default - so it is displayed in the menue box
     var i = <?php echo text($obj['appetite_score']); ?> ; //the value from last time
    document.my_form.appetite_score.options[i].defaultSelected=true;
    phq9_score += i;
    all_scores[4] = i;
    </script>
    <br></br>
</tr>
 </table>
 <table>
 <tr><td>
<span class="text" ><?php echo xlt('Feeling bad about yourself - or that you are a failure or have let yourself of your family down'); ?></span>
<select name="failure_score" onchange="update_score(5, my_form.failure_score.value);">
    <option value="0"><?php echo text($str_not); ?></option>
    <option value="1"><?php echo text($str_several); ?></option>
    <option value="2"><?php echo text($str_more); ?></option>
    <option value="3"><?php echo text($str_nearly); ?></option>
    </select>
<script>
     // set the previous value to the default - so it is displayed in the menue box
       var i = <?php echo text($obj['failure_score']); ?> ; //the value from last time
    document.my_form.failure_score.options[i].defaultSelected=true;
    phq9_score += i;
    all_scores[5] = i;
</script>
    <br></br>
    </tr>
 </table>
  <table>
  <tr><td>
<span class="text" ><?php echo xlt('Trouble concentrating on things, such as reading an article or watching videos'); ?></span>
<select name="focus_score" onchange="update_score(6, my_form.focus_score.value);">
    <option value="0"><?php echo text($str_not); ?></option>
    <option value="1"><?php echo text($str_several); ?></option>
    <option value="2"><?php echo text($str_more); ?></option>
    <option value="3"><?php echo text($str_nearly); ?></option>
    </select>
<script>
     // set the previous value to the default - so it is displayed in the menue box
     var i = <?php echo text($obj['focus_score']);?> ; //the value from last time
    document.my_form.focus_score.options[i].defaultSelected=true;
    phq9_score += i;
    all_scores[6] = i;
</script>
  <br></br>
</tr>
 </table>
   <table>
  <tr><td>
<span class="text" ><?php echo xlt('Moving or speaking slowly noted by others or fidgety or restless more than usual'); ?></span>
<select name="psychomotor_score" onchange="update_score(7, my_form.psychomotor_score.value);">
    <option value="0"><?php echo text($str_not); ?></option>
    <option value="1"><?php echo text($str_several); ?></option>
    <option value="2"><?php echo text($str_more); ?></option>
    <option value="3"><?php echo text($str_nearly); ?></option>
    </select>
<script>
     // set the previous value to the default - so it is displayed in the menue box
     var i = <?php echo text($obj['psychomotor_score']);?> ; //the value from last time
    document.my_form.psychomotor_score.options[i].defaultSelected=true;
    phq9_score += i;
    all_scores[7] = i;
</script>
  <br></br>
</tr>
 </table>
  <table>
  <tr><td>
<span class="text" ><?php echo xlt('Thoughts that you would be better off dead, or hurting yourself'); ?></span>
<select name="suicide_score" onchange="update_score(8, my_form.suicide_score.value);">
    <option value="0"><?php echo text($str_not); ?></option>
    <option value="1"><?php echo text($str_several); ?></option>
    <option value="2"><?php echo text($str_more); ?></option>
    <option value="3"><?php echo text($str_nearly); ?></option>
    </select>
<script>
     // set the previous value to the default - so it is displayed in the menue box
     var i = <?php echo text($obj['suicide_score']);?> ; //the value from last time
    document.my_form.suicide_score.options[i].defaultSelected=true;
    phq9_score += i;
    all_scores[8] = i;
</script>
  <br></br>
</tr>
 </table>
 <!-- where the final question (10)  will go if the score > 0 -->
  </table>
  <table  frame = above>
  <tr><td>
<!-- optional - only asked if score so far >0 and not included in final score -->
<!-- where the final question will go if the score > 0 -->
  <span id="q10_place"></span>
  <br>
 </table>
 <table frame=hsides>
<tr><td>
 <span id="show_phq9_score"><b><?php echo xlt("Total PHQ9 score"); ?>:</b> </td>
<!-- use this to save the individual scores in the database -->
<!-- input type="hidden" name="scores_array" -->
  <br></br>
  </tr>
  </table>
  <SCRIPT>
// only display the final question if the score is > 0
// pass the function the answer previously entered onto the form
manage_question_10 ("<?php echo text($obj["difficulty"]); ?>"); //do we need q10
update_score ("undef",phq9_score); //display total from last time
 </script>
 <br>
<input type="Submit" value="<?php echo xla('Save Form'); ?>" style="color: #483D8B"   >
&nbsp &nbsp
<input type="button" value="<?php echo attr($str_nosave_exit);?>" onclick="top.restoreSession();return( nosave_exit());" style="color: #483D8B">
 <br><br><br>
</form>

<?php
formFooter();
?>
