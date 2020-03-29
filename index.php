<?php
/**
 *
 *  Modern Localhost - A beautified index page for the localhost
 *
 *  Copyright (C) 2020  Mohamed Adhuham <me@adhuham.com>
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 */

$options = [
    /**
     * Set the theme
     * Available themes: bluey, pinky, purply
    */
    'theme' => 'grayish',

    /**
     * Exclude files or folders
     * use wildcard pattern. eg: ['.git*', '*.exe', '*.sh']
    */
    'exclude' => [ ],

    /**
     * Add extra tools 
     * [label] => '[link of the tool]
     * eg: 'phpMyAdmin' => 'http://localhost/phpMyAdmin'
    */
    'extras' => [
	    'phpinfo()' => '?phpinfo=1'
    ]
];




// display phpinfo 
if(!empty($_GET['phpinfo'])) {
	phpinfo();
	exit;
}

// server info
$apache_version = explode(' ', explode('/', apache_get_version())[1])[0];
$php_version = explode('-', phpversion())[0];
$mysql_version = explode('-', explode(' ', mysqli_get_client_info())[1])[0];

$info = [
	'Apache' => $apache_version,
	'PHP' => $php_version,
	'MySQL' => $mysql_version
];

// match a given filename against an array of patterns
function filenameMatch($patternArray, $filename) {
    if (empty($patternArray)) {
        return false;
    }
    foreach ($patternArray as $pattern) {
        if (fnmatch($pattern, $filename)) {
            return true;
        }
    }
    return false;
}

// read all items in the ./ dir
$directory_list = [];
if($handle  = opendir('./')) {
	$item_list = [];
	while(false !== ($item = readdir($handle))) {
		if($item == '..' || $item == '.' || filenameMatch($options['exclude'], $item)) {
			continue;
		}

		$item_type = is_dir($item) ? 'dir' : 'file';
		$order = ($item_type == 'dir') ? 1 : 0;
		$item_list[] = [
            'name' => $item, 
            'type' => $item_type,
			'order' => $order
        ];
	}
	$order_column = array_column($item_list, 'order');
	array_multisort($order_column, SORT_DESC, $item_list);
	$directory_list = $item_list;
	closedir($handle);
}


$themes = [
    'bluey' => [
        'background' => '#102252',
        'accent' => '#d9e009',
        'secondary' => '#efffc1',
    ],
    'grayish' => [
        'background' => '#353331',
        'accent' => '#fbdf9a',
        'secondary' => '#fff3d4',
    ],
    'pinky' => [
        'background' => '#5d0431',
        'accent' => '#d7da13',
        'secondary' => '#fcfda5',
    ],
    'purply' => [
        'background' => '#3a0440',
        'accent' => '#47e6ba',
        'secondary' => '#bcffec',
    ]
];

$theme = $themes[$options['theme']] ?? 'grayish';

$favicon_data = 'data:image/png;base64,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';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="icon" href="<?=$favicon_data?>">
	<style>
        :root {
            --color-bkg: <?=$theme['background']?>;
            --color-accent: <?=$theme['accent']?>;
            --color-secondary: <?=$theme['secondary']?>;
        }
		body {
			font-family: monospace;
			background: var(--color-bkg);
			margin: 0;
			padding: 0;
			min-height: 100vh;
			display: flex;
			justify-content: center;
            overflow: hidden;
		}
		h2 {
            font-size: 16px;
            color: rgba(255, 255, 255, 0.65);
            font-weight: normal;
            margin-top: 0;
            margin-bottom: 20px;
            letter-spacing: 3px;
		}
		main {
            width: 600px;
			margin: 80px 30px;
			display: flex;
			justify-content: center;
			color: #FFF;
            max-height: calc(100vh - (80px * 2));
            overflow: hidden;
		}
        main > .projects {
            flex: 2;
        }
        main > aside {
            flex: 1;
        }


		.info {
            margin-bottom: 40px;
        }
		.info .table {}
		.info .table > div {
			margin: 7px 0;
            color: rgba(255, 255, 255, 0.8);
		}
		.info .table > div > span {}
		.info .table > div > span:first-child {
			width: 50px;
			display: inline-block;
		}
		.info .table > div > span:last-child {
			font-weight: bold;
			padding: 0 3px;
			border-radius: 3px;
		}

		.projects {
            padding-right: 80px;
            padding-left: 2px;
		}
        .projects .content {
            height: calc(100% - 80px);
        }
        .projects .content input {
            background: rgba(255, 255, 255, 0.06);
            border: none;
            padding: 9px 15px;
            margin-bottom: 10px;
            width: calc(100% - 30px);
            border-radius: 4px;
            font-family: monospace;
            font-weight: bold;
            color: #CCC;
            height: 15px;
            transition: all 0.3s ease;
        }
        .projects .content input::placeholder {
            color: rgba(255, 255, 255, 0.28);
        }
        .projects .content input:focus,
        .projects .content input:active {
            outline: none;
        }
        .projects .content input:focus {
            box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.10);
            background: rgba(204, 204, 204, 0.05);
        }
		.projects ul {
			list-style-type: none;
            margin: 0;
            padding: 0;
            height: calc(100% - 25px);
            overflow-y: auto;
		}
        .projects ul::-webkit-scrollbar {
            display: none;
        }
		.projects ul li {
			margin: 8px 0;
		}
        .projects ul li.hidden {
            display: none;
        }
		.projects ul li a {
			text-decoration: none;
			border-radius: 3px;
			font-size: 13px;
			padding: 2px 4px;
		}
		.projects ul li.dir a {
			color: var(--color-accent);
			font-weight: bold;
		}
		.projects ul li.dir a:hover, 
		.projects ul li.dir a:focus {
			background-color: var(--color-accent);
			color: var(--color-bkg);
            outline: none;
		}
		.projects ul li.file a {
			color: var(--color-secondary);
		}
		.projects ul li.file a:hover, 
		.projects ul li.file a:focus {
			background-color: var(--color-secondary);
			color: var(--color-bkg);
            outline: none;
		}

		.tools {
		}
		.tools ul {
			list-style-type: none;
			padding: 0;
			margin-left: 4px;
		}
		.tools ul li::before {
			content: '↪ ';
		}
		.tools ul li {
			margin: 6px 0;
		}
		.tools ul li a {
			color: var(--color-accent);
			text-decoration: none;
			border-radius: 3px;
			font-size: 14px;
			padding: 2px 4px;
		}
		.tools ul li a:hover {
			background-color: var(--color-accent);
			color: var(--color-bkg);
		}

        @media screen and (max-width: 500px) {
            main {
                margin: 40px 20px;
                max-height: calc(100vh - (40px * 2));
            }
            main > aside {
                display: none;
            }

            .projects {
                padding-left: 0;
                padding-right: 0;
            }

        }
	</style>
</head>

<body>
	<main>
		<div class="projects">
			<h2>projects</h2>
            <div class="content">
                <input type="text" placeholder="Search" class="search">
                <ul>
                    <?php foreach($directory_list as $item): ?>
                    <li class="<?=$item['type']?>">
                        <a href="<?=$item['name']?>">
                            <?php $slash = $item['type'] == 'dir' ? '/' : null; ?>
                            <?=$item['name'].$slash;?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
		</div>
        <aside>
            <div class="info">
                <h2>info</h2>
                <div class="table">
                    <?php foreach($info as $label => $value): ?>
                    <div class="<?=$label?>">
                        <span><?=$label?></span>
                        <span><?=$value?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php if (!empty($options['extras'])): ?>
            <div class="tools">
                <h2>extras</h2>
                <ul>
                    <?php foreach($options['extras'] as $label => $link): ?>
                    <li><a href="<?=$link?>"><?=$label?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
        </aside>
	</main>
    <script>
        const searchInput = document.querySelector('input.search');
        const projectContent = document.querySelector('.projects .content ul');

        searchInput.focus();
        searchInput.addEventListener('keyup', (e) => {
            let val = searchInput.value.trim();

            projectContent.querySelectorAll('li').forEach((el) => {
                // jump to the first displayed dir/file on enter
                if (e.keyCode == 13 && val != '') {
                    const firstResult = projectContent.querySelector('li:not(.hidden) a');
                    const loc = firstResult.getAttribute('href');
                    searchInput.value = '';
                    window.location = loc;
                }

                if (val == '') {
                    el.classList.remove('hidden');
                }
                else if (el.innerText.indexOf(val) <= -1) {
                    el.classList.add('hidden');
                }
            });
        });
    </script>
</body>
</html>
