<?php

return array(

	'cdn' => env('CDN_URL', false),
	'asset_cdn' => env('CDN_ASSET_URL', false),

	'available_languages' => array('en', 'ja', 'de', 'fr'),
	'full_languages' => array(
		'en' => 'English',
		'fr' => 'Français',
		'de' => 'Deutsch',
		'ja' => '日本語',
	),
	'default_language' => 'en',
	// 'donation_slogans' => array(
	// 	"Support Alcoholism, <a href='#buymeabeer' id='buymeabeer'>Buy me a beer!</a>",
	// 	#"Keep the site ad free, <a href='#buymeabeer' id='buymeabeer'>The best AdBlock is Donating!</a>",
	// 	"Show my wife it's not just a hobby, <a href='#buymeabeer' id='buymeabeer'>Donate!</a>",
	// 	#"Stable servers aren't free, <a href='#buymeabeer' id='buymeabeer'>Support the site!</a>",
	// 	"I've spent more time building this than playing, <a href='#buymeabeer' id='buymeabeer'>Help me relax!</a>",
	// 	"At least you know I'm not a Nigerian Prince, <a href='#buymeabeer' id='buymeabeer'>Donate!</a>",
	// 	#"Help the site out, <a href='#buymeabeer' id='buymeabeer'>Like it on Facebook!</a>",
	// ),
	'cache_length' => '10080', // Minutes - 60 * 24 * 7 -- One Month (php artisan cache:clear should flush it sooner)
	'equipment_roles' => array('Main Hand','Off Hand','Head','Body','Hands','Waist','Legs','Feet','Neck','Ears','Wrists','Right Ring','Right Ring'),
	'gear_focus' => array(
		'LNC,PGL,DRG,MNK,BRD,ARC,ROG,NIN' => array(
			'Dexterity',
			'Critical Hit Rate',
			'Skill Speed',
		),
		'GLA,MRD,PLD,WAR' => array(
			'Strength',
			'Skill Speed',
			'Parry',
		),
		'THM,BLM,ACN,SMN' => array(
			'Intelligence',
			'Spell Speed',
			'Piety',
		),
		'CNJ,SCH,WHM' => array(
			'Mind',
			'Spell Speed',
			'Piety',
		),
		'CRP,BSM,ARM,GSM,LTW,WVR,ALC,CUL' => array(
			'Control',
			'CP',
			'Craftsmanship',
		),
		'MIN,BTN,FSH' => array(
			'Gathering',
			'GP',
			'Perception',
		),
	),
	'job_ids' => array(
		'crafting' => array(
			8, // CRP
			9, // BSM
			10, // ARM
			11, // GSM
			12, // LTW
			13, // WVR
			14, // ALC
			15, // CUL
		),
		'gathering' => array(
			16, // MIN
			17, // BTN
			18, // FSH
		),
		'fishing' => 18,
		'basic_melee' => array(
			1, // GLA
			2, // PGL
			3, // MRD
			4, // LNC
			5, // ARC
			29, // ROG
		),
		'basic_magic' => array(
			6, // CNJ
			7, // THM
			26, // ACN
		),
		'advanced_melee' => array(
			19, // PLD
			20, // MNK
			21, // WAR
			22, // DRG
			23, // BRD
			30, // NIN
		),
		'advanced_magic' => array(
			24, // WHM
			25, // BLM
			27, // SMN
			28, // SCH
		),
	),
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
	'map' => array(
		'shroud' => array(
			'area' => array(
				'id' => 23,
				'name' => 'The Black Shroud (Gridania)',
				'short_name' => 'Black Shroud',
				'img' => '/img/maps/the-black-shroud-the-black-shroud-region-01' // .png
			),
			'regions' => array(
				'north' => array(
					'id' => 57,
					'name' => '',
					'img' => '/img/maps/the-black-shroud-north-shroud-f1f4-00', // .png
					'top' => '-120', // px
					'left' => '-90', // px
				),
				'gridania' => array(
					'id' => 51, // Also 52, 53
					'id_also' => '52,53',
					'name' => '',
					'img' => '/img/maps/the-black-shroud-gridania', // .png
					'top' => '70', // px
					'left' => '220', // px
				),
				'east' => array(
					'id' => 55,
					'name' => '',
					'img' => '/img/maps/the-black-shroud-east-shroud-f1f2-00', // .png
					'top' => '150', // px
					'left' => '560', // px
				),
				'central' => array(
					'id' => 54,
					'name' => '',
					'img' => '/img/maps/the-black-shroud-central-shroud-f1f1-00', // .png
					'top' => '290', // px
					'left' => '110', // px
				),
				'south' => array(
					'id' => 56,
					'name' => '',
					'img' => '/img/maps/the-black-shroud-south-shroud-f1f3-00', // .png
					'top' => '520', // px
					'left' => '310', // px
				),
			),
		),
		'thanalan' => array(
			'area' => array(
				'id' => 24,
				'name' => 'Thanalan (Ul\'dah)',
				'short_name' => 'Thanalan',
				'img' => '/img/maps/thanalan-thanalan-region-02' // .png
			),
			'regions' => array(
				'western' => array(
					'id' => 42,
					'name' => '',
					'img' => '/img/maps/thanalan-western-thanalan-w1f1-00', // .png
					'top' => '470', // px
					'left' => '20', // px
				),
				'uldah' => array(
					'id' => 39, // Also 40, 41
					'id_also' => '40,41',
					'name' => '',
					'img' => '/img/maps/thanalan-uldah---steps-of-thal', // .png
					'top' => '530', // px
					'left' => '290', // px
				),
				'southern' => array(
					'id' => 45,
					'name' => '',
					'img' => '/img/maps/thanalan-southern-thanalan-w1f4-01', // .png
					'top' => '470', // px
					'left' => '520', // px
				),
				'northern' => array(
					'id' => 46,
					'name' => '',
					'img' => '/img/maps/thanalan-northern-thanalan-w1f5-00', // .png
					'top' => '-80', // px
					'left' => '210', // px
				),
				'eastern' => array(
					'id' => 44,
					'name' => '',
					'img' => '/img/maps/thanalan-eastern-thanalan-w1f3-00', // .png
					'top' => '90', // px
					'left' => '530', // px
				),
				'central' => array(
					'id' => 43,
					'name' => '',
					'img' => '/img/maps/thanalan-central-thanalan-w1f2-00', // .png
					'top' => '220', // px
					'left' => '230', // px
				),
			),
		),
		'noscea' => array(
			'area' => array(
				'id' => 22,
				'name' => 'La Noscea (Limsa Lominsa)',
				'short_name' => 'La Noscea',
				'img' => '/img/maps/la-noscea-la-noscea-region-00' // .png
			),
			'regions' => array(
				'western' => array(
					'id' => 33,
					'name' => '',
					'img' => '/img/maps/la-noscea-western-la-noscea-s1f4-00', // .png
					'top' => '110', // px
					'left' => '-80', // px
				),
				'upper' => array(
					'id' => 34,
					'name' => '',
					'img' => '/img/maps/la-noscea-upper-la-noscea-s1f5-00', // .png
					'top' => '40', // px
					'left' => '220', // px
				),
				'middle' => array(
					'id' => 30,
					'name' => '',
					'img' => '/img/maps/la-noscea-middle-la-noscea-s1f1-00', // .png
					'top' => '400', // px
					'left' => '250', // px
				),
				'lower' => array(
					'id' => 31,
					'name' => '',
					'img' => '/img/maps/la-noscea-lower-la-noscea-s1f2-00', // .png
					'top' => '400', // px
					'left' => '400', // px
				),
				'limsa' => array(
					'id' => 27, // Also 28, 29
					'id_also' => '28,29',
					'name' => '',
					'img' => '/img/maps/la-noscea-limsa-lominsa', // .png
					'top' => '550', // px
					'left' => '20', // px
				),
				'eastern' => array(
					'id' => 32,
					'name' => '',
					'img' => '/img/maps/la-noscea-eastern-la-noscea-s1f3-00', // .png
					'top' => '0', // px
					'left' => '520', // px
				),
				'outer' => array(
					'id' => 350,
					'name' => '',
					'img' => '/img/maps/la-noscea-outer-la-noscea-s1f6-00', // .png
					'top' => '0', // px
					'left' => '220', // px
				),
			),
		),
		'coerthas' => array(
			'area' => array(
				'id' => 25, // Also 26
				'id_also' => '26',
				'name' => 'Coerthas / Mor Dhona',
				'short_name' => 'Mor Dhona',
				'img' => '/img/maps/mor-dhona-mor-dhona-region-04' // .png
			),
			'regions' => array(
				'coerthas-central' => array(
					'id' => 63,
					'name' => '',
					'img' => '/img/maps/coerthas-coerthas-central-highlands-r1f1-00', // .png
					'top' => '0', // px
					'left' => '40', // px
				),
				'mor-dhona' => array(
					'id' => 67,
					'name' => '',
					'img' => '/img/maps/mor-dhona-mor-dhona-l1f1-01', // .png
					'top' => '380', // px
					'left' => '140', // px
				),
			),
		),
	),
	'servers' => array(
		'Adamantoise',
		'Aegis',
		'Alexander',
		'Anima',
		'Asura',
		'Atomos',
		'Bahamut',
		'Balmung',
		'Behemoth',
		'Belias',
		'Brynhildr',
		'Cactuar',
		'Carbuncle',
		'Cerberus',
		'Chocobo',
		'Coeurl',
		'Diabolos',
		'Durandal',
		'Excalibur',
		'Exodus',
		'Faerie',
		'Famfrit',
		'Fenrir',
		'Garuda',
		'Gilgamesh',
		'Goblin',
		'Gungnir',
		'Hades',
		'Hyperion',
		'Ifrit',
		'Ixion',
		'Jenova',
		'Kujata',
		'Lamia',
		'Leviathan',
		'Lich',
		'Malboro',
		'Mandragora',
		'Masamune',
		'Mateus',
		'Midgardsormr',
		'Moogle',
		'Odin',
		'Pandaemonium',
		'Phoenix',
		'Ragnarok',
		'Ramuh',
		'Ridill',
		'Sargatanas',
		'Shinryu',
		'Shiva',
		'Siren',
		'Tiamat',
		'Titan',
		'Tonberry',
		'Typhon',
		'Ultima',
		'Ultros',
		'Unicorn',
		'Valefor',
		'Yojimbo',
		'Zalera',
		'Zeromus',
		'Zodiark',
	),
);