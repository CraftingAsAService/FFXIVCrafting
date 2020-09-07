<?php

return [

	'max_level' => 80,

	'cdn' => env('CDN_URL', false),
	'asset_cdn' => env('CDN_ASSET_URL', false),

	'available_languages' => ['en', 'ja', 'de', 'fr'],
	'full_languages' => [
		'en' => 'English',
		'fr' => 'Français',
		'de' => 'Deutsch',
		'ja' => '日本語',
	],
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
	'cache_length' => '604800', // in Seconds - 60 * 60 * 24 * 7 -- One Month (php artisan cache:clear should flush it sooner)
	'equipment_roles' => array('Main Hand','Off Hand','Head','Body','Hands','Waist','Legs','Feet','Ears','Neck','Wrists','Left Ring','Right Ring'),
	'gear_focus' => array(
		'PGL,MNK,SAM'  => array(
			'Strength',
			'Critical Hit',
			'Skill Speed',
		),
		'LNC,DRG,BRD,ARC,ROG,NIN,MCH,DNC' => array(
			'Dexterity',
			'Critical Hit',
			'Skill Speed',
		),
		'GLA,MRD,PLD,WAR,DRK,GNB' => array(
			'Strength',
			'Skill Speed',
			'Tenacity',
		),
		'THM,BLM,ACN,SMN,RDM,BLU' => array(
			'Intelligence',
			'Spell Speed',
		),
		'CNJ,SCH,WHM,AST' => array(
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
	// Stat weight data gathered from http://ffxiv.ariyala.com/
	// Missing MCH, DRK, AST data; assumptions made
	'stat_weights' => [
		'CRP,BSM,ARM,GSM,LTW,WVR,ALC,CUL' => [
			'Control'			=> 1,
			'CP'				=> 1,
			'Craftsmanship'		=> 1,
		],
		'MIN,BTN,FSH' => [
			'Gathering'			=> 1,
			'GP'				=> 1,
			'Perception'		=> 1,
		],
		'ARC,BRD,MCH,DNC' => [
			'Physical Damage'	=> 11.602,
			'Dexterity'			=> 1,
			'Direct Hit Rate'			=> 0.0647, // Has a "Minimum" softcap (647); giving it a low score so it's more than nothing
			'Critical Hit'	=> 0.224,
			'Determination'		=> 0.14,
			'Skill Speed'		=> 0.111,
		],
		'ROG,NIN' => [
			'Physical Damage'	=> 10.775,
			'Dexterity'			=> 1,
			'Direct Hit Rate'			=> 0.0647, // Has a "Minimum" softcap (647); giving it a low score so it's more than nothing
			'Critical Hit'	=> 0.166,
			'Determination'		=> 0.141,
			'Skill Speed'		=> 0.074,
		],
		'PGL,MNK,SAM,GNB' => [
			'Physical Damage'	=> 10.714,
			'Strength'			=> 1,
			'Direct Hit Rate'			=> 0.0647, // Has a "Minimum" softcap (647); giving it a low score so it's more than nothing
			'Critical Hit'	=> 0.167,
			'Determination'		=> 0.139,
			'Skill Speed'		=> 0.116,
		],
		'LNC,DRG' => [
			'Physical Damage'	=> 10.625,
			'Strength'			=> 1,
			'Direct Hit Rate'			=> 0.0647, // Has a "Minimum" softcap (647); giving it a low score so it's more than nothing
			'Critical Hit'	=> 0.162,
			'Determination'		=> 0.139,
			'Skill Speed'		=> 0.104,
		],
		'GLA,PLD,MRD,WAR,DRK' => [
			'Physical Damage'	=> 8.732,
			'Strength'			=> 1,
			'Direct Hit Rate'			=> 0.0647, // Has a "Minimum" softcap (647); giving it a low score so it's more than nothing
			'Critical Hit'	=> 0.204,
			'Determination'		=> 0.325,
			'Skill Speed'		=> 0.178,
			'Vitality'			=> 1,
			'Tenacity'				=> 1,
			// Defense matters, but it's not a stat focus
			'Defense'			=> 0.1,
			'Block Strength'	=> 0.05,
			'Block Rate'		=> 0.05,
		],
		'THM,BLM,RDM,BLU' => [ // Guess on BLU
			'Physical Damage'	=> 6.726,
			'Intelligence'		=> 1,
			'Direct Hit Rate'			=> 0.0540, // Has a "Minimum" softcap (540); giving it a low score so it's more than nothing
			'Critical Hit'	=> 0.234,
			'Determination'		=> 0.246,
			'Spell Speed'		=> 0.281,
		],
		'ACN,SMN' => [
			'Physical Damage'	=> 11.602,
			'Intelligence'		=> 1,
			'Direct Hit Rate'			=> 0.0540, // Has a "Minimum" softcap (540); giving it a low score so it's more than nothing
			'Critical Hit'	=> 0.147,
			'Determination'		=> 0.137,
			'Spell Speed'		=> 0.119,
		],
		'CNJ,WHM,SCH,AST' => [
			'Physical Damage'	=> 8.732,
			'Mind'				=> 1,
			'Direct Hit Rate'			=> 0, // Healers don't need Direct Hit Rate
			'Critical Hit'	=> 0.204,
			'Determination'		=> 0.325,
			'Spell Speed'		=> 0.178,
		],
	],
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
			31, // MCH
			32, // DRK
			34, // SAM
			37, // GNB
			38, // DNC
		),
		'advanced_magic' => array(
			24, // WHM
			25, // BLM
			27, // SMN
			28, // SCH
			33, // AST
			35, // RDM
			36, // BLU
		),
	),
	'roles' => [
		'tank' => [
			'PLD',
			'WAR',
			'DRK',
			'GNB',
		],
		'healer' => [
			'WHM',
			'SCH',
			'AST',
		],
		'ranged' => [
			'BRD',
			'MCH',
			'DNC',
		],
		'melee' => [
			'MNK',
			'DRG',
			'NIN',
			'SAM',
		],
		'magic' => [
			'BLM',
			'SMN',
			'RDM',
			'BLM',
		],
	],
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
];