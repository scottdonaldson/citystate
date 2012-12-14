<?php 

// Random name generator.
// Just for fun.

$first = array(
	'Ali',
	'Avis',
	'Becky',
	'Byron',
	'Christina',
	'Darren',
	'Dollie',
	'Earl',
	'Ellen',
	'Ernest',
	'Guy',
	'Hal',
	'Matthew',
	'Jami',
	'Julio',
	'Karen',
	'Keith',
	'Ken',
	'Kendrick',
	'Krista',
	'Neva',
	'Nolan',
	'Raphael',
	'Rod',
	'Roger',
	'Ronald',
	'Suzanne',
	'Ted',
	'Tonya',
	'Travis'
);

$last = array(
	'Allen',
	'Arb',
	'Bell',
	'Benton',
	'Boliou',
	'Burton',
	'Carleton',
	'Cassat',
	'Cave',
	'Cowling',
	'Davis',
	'Eugster',
	'Evans',
	'Farm',
	'Goodhue',
	'Goodsell',
	'Gould',
	'Hill',
	'Hulings',
	'Laird',
	'Leighton',
	'Memorial',
	'Moses',
	'Mudd',
	'Musser',
	'Myers',
	'Nason',
	'Nourse',
	'Oden',
	'Olin',
	'Parish',
	'Sayles-Hill',
	'Scoville',
	'Sevy',
	'Watson',
	'Weitz',
	'Wilson',
	'Willis'
);

echo $name = $first[rand(0, count($first) - 1)].' '.$last[rand(0, count($last) - 1)];

?>