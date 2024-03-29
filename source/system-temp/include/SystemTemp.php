<?PHP
/* Copyright 2012-2023, Bergware International.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * Plugin development contribution by gfjardim
 */
?>
<?
$plugin = 'dynamix.system.temp';
$docroot = $docroot ?? $_SERVER['DOCUMENT_ROOT'] ?: '/usr/local/emhttp';

// add translations
$_SERVER['REQUEST_URI'] = 'tempsettings';
require_once "$docroot/webGui/include/Translations.php";

$autofan = "dynamix.system.autofan";
$script  = "$docroot/plugins/$autofan/scripts/rc.autofan";

function my_temp($val, $unit, $dot) {
  return ($val ? ($unit=='F' ? round(9/5*$val+32) : str_replace('.',$dot,$val)) : '&sdot;&sdot;&sdot;')."&thinsp;&deg;".$unit;
}
function my_rpm($val){
  return ($val ?: '&sdot;&sdot;&sdot;')."&thinsp;"._('rpm');
}
function load($fan) {
  return is_numeric($fan) ? " ({$fan}%)" : "";
}
function get_autofan() {
  global $script;
  return is_executable($script) ? array_map('load',explode(' ',exec("$script speed"))) : [];
}
$temp = [];
$sensors = explode(' ',exec("sensors -A|awk 'BEGIN{cpu=\"\";mb=\"\";fan=\"\"}{if (/^CPU Temp/) cpu=\$3*1; if (/^MB Temp/) mb=\$3*1; if (/^Array Fan/) fan=fan\" \"\$3*1} END{print cpu,mb fan}'"));
$fans = get_autofan();
$temp[] = "<span title='"._('Processor')."'><i class='fa fa-thermometer-0' style='margin:0 6px 0 24px'></i>".my_temp($sensors[0], $_POST['unit']??'C', $_POST['dot']??'.')."</span>";
$temp[] = "<span title='"._('Mainboard')."'><i class='fa fa-thermometer-0' style='margin:0 6px 0 24px'></i>".my_temp($sensors[1], $_POST['unit']??'C', $_POST['dot']??'.')."</span>";
for ($i=2; $i<count($sensors); $i++) $temp[] = "<span title='"._('Array fan')."'><i class='fa fa-flag-o' style='margin:0 6px 0 24px'></i>".my_rpm($sensors[$i]).($fans[$i-2]??'')."</span>";
echo "<span id='temp' style='margin-right:24px'>".implode($temp)."</span>";
?>
