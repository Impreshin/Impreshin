<?php

$cfg['currency']['sign'] = "R ";
$cfg['currency']['separator'] = " ";

$cfg['news']['path'] = "http://admin.impreshin.info/news/json";


$cfg['DB']['host'] = "127.0.0.1";
$cfg['DB']['username'] = "username";
$cfg['DB']['password'] = "password";
$cfg['DB']['database'] = "adbooker_v5";

$cfg['package']['host'] = "127.0.0.1";
$cfg['package']['username'] = "username";
$cfg['package']['password'] = "password";
$cfg['package']['database'] = "impreshin-admin";



$cfg['upload']['material'] = false;
$cfg['upload']['pages'] = false;
$cfg['upload']['folder'] = $_SERVER['DOCUMENT_ROOT'] .  "/uploads/";
$cfg['debug']['highlightfrom'] = 0.7;

$cfg['apps'] = array("ab","nf");
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