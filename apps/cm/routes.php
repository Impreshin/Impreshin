<?php

$routes = array();

// main app
$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/cm/form',
	"controller"=>'apps\cm\controllers\form->page',
	"a"=>true,
	"l"=>true,
);
$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/cm/form/@ID',
	"controller"=>'apps\cm\controllers\form->page',
	"a"=>true,
	"l"=>true,
);
$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/cm',
	"controller"=>'apps\cm\controllers\front->page',
	"a"=>true,
	"l"=>true,
);
$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/cm/calendar',
	"controller"=>'apps\cm\controllers\calendar->page',
	"a"=>true,
	"l"=>true,
);
$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/cm/watch',
	"controller"=>'apps\cm\controllers\watchlist->page',
	"a"=>true,
	"l"=>true,
);
$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/cm/companies',
	"controller"=>'apps\cm\controllers\companies->page',
	"a"=>true,
	"l"=>true,
);
$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/cm/people',
	"controller"=>'apps\cm\controllers\people->page',
	"a"=>true,
	"l"=>true,
);
$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/cm/reports/interactions',
	"controller"=>'apps\cm\controllers\reports\people_interactions->page',
	"a"=>true,
	"l"=>true,
);
// administration




$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/cm/admin/details_types',
	"controller"=>'apps\cm\controllers\admin\details_types->page',
	"a"=>true,
	"l"=>true,
);
$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/cm/admin/import',
	"controller"=>'apps\cm\controllers\admin\import->page',
	"a"=>true,
	"l"=>true,
);




$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/cm/admin/dates',
	"controller"=>'apps\cm\controllers\admin\dates->page',
	"a"=>true,
	"l"=>true,
);




$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/cm/admin/publications',
	"controller"=>'apps\cm\controllers\admin\publications->page',
	"a"=>true,
	"l"=>true,
);
$routes[] =	array(
	"method"=>'GET',
	"path"=>'/app/cm/admin/users',
	"controller"=>'apps\cm\controllers\admin\users->page',
	"a"=>true,
	"l"=>true,
);


// utilities





$router['cm'] = $routes;

$apps["cm"] = array(
		"name" => "Contacts",
		"description"=>"A Contact Manager"
);



?>
