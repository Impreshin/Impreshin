<?php

$cfg['TZ'] = "Africa/Johannesburg";
$cfg['localization']['language'] = "en_ZA";
$cfg['localization']['currency'] = "ZAR";





$cfg['DB']['host'] = "127.0.0.1";
$cfg['DB']['username'] = "username";
$cfg['DB']['password'] = "password";
$cfg['DB']['database'] = "adbooker_v5";





$cfg['upload']['material'] = false;
$cfg['upload']['pages'] = false;
$cfg['upload']['folder'] = $_SERVER['DOCUMENT_ROOT'] .  "/uploads/";
$cfg['debug']['highlightfrom'] = 0.7;

$cfg['apps'] = array("ab","nf","pf");
$cfg['gzip'] = false;

$cfg['backup'] = $_SERVER['DOCUMENT_ROOT'] . "/backups/";
$cfg['online'] = true;



$cfg['git']['path']="github.com/Impreshin/Impreshin.git";
$cfg['git']['username']="ImpreshinDeploy";
$cfg['git']['password']="impreshindeploy015";
$cfg['git']['branch']="master";
$cfg['git']['token']="e0619b48df9ba55f7f131a360bdf3ee3";


$cfg['git']['docs']['path'] = "github.com/Impreshin/Impreshin-Docs.git";
$cfg['git']['docs']['username'] = "ImpreshinDeploy";
$cfg['git']['docs']['password'] = "impreshindeploy015";
$cfg['git']['docs']['branch'] = "master";


$cfg['default_colours'] = array("1","2","3");
$cfg['form']['max_accounts'] = 300;

$cfg['nf']['default_cm_calc_css'] = '#cm-block {width : 88px; font-size : 10px; text-align : justify;	font-family:"Times New Roman", Times, serif; line-height:100%;} #cm-block p {margin-top    : 3px; margin-bottom : 3px; text-indent   : 0.3cm;}';

$cfg['nf']['whitelist_tags'] = "b,i,em,strong,p,br,hr,sub,sup,u,underline";


$cfg['nf']['languages'] = array(
	"en_gb"=>"English",
	"af_ZA"=>"Afrikaans"
	
);

$cfg['system_messages']=true;
$cfg['HOST_DOMAIN']="app.impreshin.com";
$cfg['HOST_DOMAIN_COMPLETE']="https://".$cfg['HOST_DOMAIN'];
