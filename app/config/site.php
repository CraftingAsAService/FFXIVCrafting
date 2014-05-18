<?php

return array(
	'donation_slogans' => array(
		"Support Alcoholism, <a href='#buymeabeer' id='buymeabeer'>Buy me a beer!</a>",
		"Keep the site ad free, <a href='#buymeabeer' id='buymeabeer'>The best AdBlock is Donating!</a>",
		"Show my wife it's not just a hobby, <a href='#buymeabeer' id='buymeabeer'>Donate!</a>",
		#"Stable servers aren't free, <a href='#buymeabeer' id='buymeabeer'>Support the site!</a>",
		"I've spent more time building this than playing, <a href='#buymeabeer' id='buymeabeer'>Help me relax!</a>",
		"At least you know I'm not a Nigerian Prince, <a href='#buymeabeer' id='buymeabeer'>Donate!</a>",
		#"Help the site out, <a href='#buymeabeer' id='buymeabeer'>Like it on Facebook!</a>",
	),
	'cache_length' => '524160', // Minutes - 60 * 24 * 7 * 52 -- One Year (php artisan cache:clear flushes it sooner)
	'equipment_roles' => array('Main Hand','Off Hand','Head','Body','Hands','Waist','Legs','Feet','Neck','Ears','Wrists','Right Ring','Right Ring'),
	'defined_slots' => array(
		1	=> 'Main Hand',
		2	=> 'Off Hand',
		3	=> 'Head',
		4	=> 'Body',
		5	=> 'Hands',
		6	=> 'Waist',
		7	=> 'Legs',
		8	=> 'Feet',
		9	=> 'Ears',
		10	=> 'Neck',
		11	=> 'Wrists',
		12	=> 'Right Ring',
		13	=> 'Main Hand & Off Hand',
		//14 	=> '???',
		15	=> 'Body & Head',
		16	=> 'Body, Hands, Legs & Feet',
		17	=> 'Soul Crystal',
		18	=> 'Legs & Feet',
		19	=> 'Body, Head, Hands, Legs & Feet',
		//20 	=> '???',
		21	=> 'Body, Legs & Feet'
	),
	'slot_alias' => array(
		13	=> 1, // 'Main-Hand & Off-Hand',
		15	=> 4, //'Body & Head',
		16	=> 4, // 'Body, Hands, Legs & Feet',
		18	=> 7, // 'Legs & Feet',
		19	=> 4, // 'Body, Head, Hands, Legs & Feet',
		21	=> 4, // 'Body, Legs & Feet'
	),
	'slot_cannot_equip' => array(
		13	=> array(2), // 'Main-Hand & Off-Hand',
		15	=> array(3), // 'Body & Head',
		16	=> array(5,7,8), // 'Body, Hands, Legs & Feet',
		18	=> array(8), // 'Legs & Feet',
		19	=> array(3,5,7,8), // 'Body, Head, Hands, Legs & Feet',
		21	=> array(7,8), // 'Body, Legs & Feet'
	),

	'available_languages' => array('en', 'ja', 'de', 'fr'),
	'default_language' => 'en',
);