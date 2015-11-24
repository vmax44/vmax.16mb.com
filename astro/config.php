<?PHP
$config=[
	"scrapeData"=>[
		[".infobox tr[0] td[0]", //selector
			"innertext",		 //attribute
			"tickets"],  		 //field name in database
		[".infobox tr[0] td["]
	],
	"groupsInReport" => [
		"Flow"			=>	"flow",   //"Group name in reports" => "field name in database"
		"Flow EN"		=>	"flowen",
//		"Harlequin"		=>	"harlequin_closed"
	]
];
?>