<?php
#PHP Source Code
require "./lib/phpspellcheck/include.php"; // this path must lead to you PHP Spell Check Install folder

//For a spell-check button that opens in a popup dialog.
echo '<textarea></textarea>';
$mySpell = new SpellCheckButton();
$mySpell->InstallationPath = "./lib/phpspellcheck/";
$mySpell->Fields = "ALL";
echo $mySpell->SpellImageButton();

//For inline "spell-as-you-type" on right click
$mySpell = new SpellAsYouType();
$mySpell->InstallationPath = "./lib//phpspellcheck/";
$mySpell->Fields = "ALL";
echo $mySpell->Activate();
 
 ?>
 