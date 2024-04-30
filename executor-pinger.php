<?php

/**
 * See https://getemoji.com/
 */
const ICON_SETS = [
  'circles' => '🔴🟡🟣🔵🟢',
  'squares' => '🟥🟨🟪🟦🟩',
  'fruit' => '🍅🍋🍇🫐🍏',
  'dot' => '⬤⬤⬤⬤⬤',
];
const COLOR_SETS = [
  'circles' => '#fa5452,#ffd162,#b65ec1,#2189d4,#8ebd66',
  'squares' => '#fa5452,#ffd162,#b65ec1,#2189d4,#8ebd66',
  'fruit' => '#ff3343,#ffc855,#8f5ca0,#5e79cc,#97ca69',
  'dot' => 'red,yellow,magenta,cyan,green',
];

const DEFAULT_SPEED_VALUES = '1000,120,60,30';

const DEFAULT_HOST = '8.8.8.8';

/**
 * Format latency time.
 *
 * @param float $time
 *   Latency n milliseconds.
 *
 * @return string
 *   Formatted latency.
 */
function format_ping_time($time, $display_map) {
  $icon = '';
  $color = '';
  if (!$time = round(floatval($time))) {
    $time = '❌';
  }
  else {
    foreach ($display_map as $min => $values) {
      if ($time >= $min) {
        $icon = $values['icon'];
        $color = $values['color'];
        break;
      }
    }
    if ($time >= 1000) {
      $time = round($time / 1000, 1) . 's';
    }
    else {
      $time .= 'ms';
    }
  }
  if (!$color) {
    $color = '#fff';
  }
  return "<executor.markup.true> <span foreground='{$color}'>"
    . '<b>'
    . trim($icon . ' ' . $time)
    . '</b>'
    .  "</span>";
}

function get_latency($host) {
  $latency = `( ping $host -c 1 | grep -E -o 'time=[0-9.]+' | cut -f2 -d'=') 2>/dev/null`;
  return trim($latency);
}

$options = getopt('', ['icons::', 'speed::', 'colors::', 'host::', 'help::']);
if (isset($options['help'])) {
  print "Will show the current ping time for a given server along with an icon that corresponds to the time.\n\n"
    . "Usage:\n"
    . "php executor-pinger.php [--icons=ICONSET] [--speed=SPEED_VALUES] [--colors=COLORS] [--host=HOST]\n\n"
    .  "Where:\n"
    . "  ICONSET - One of circles, squares, fruit, or random. Or, can be a string of icons (including emojis). Default: circles\n"
    . "  SPEED_VALUES - Comma-separated list of speed values to go with icons. Number of items must be one fewer than the number of icons. Default: 1000,120,60,30\n"
    . "  COLORS - Comma-separated list of color values to use for the time text with each icon. Use 'none' to use white for all. Number of items must be the same as the number of icons. Default: Color set for icons or 'none'\n"
    . "  HOST - IP address to ping. Default: 8.8.8.8\n";
  exit(0);
}

$icons = [];
$iconset = $options['icons'] ?? 'circles';
if ($iconset == 'random') {
  $iconset = array_keys(ICON_SETS)[rand(0, count(ICON_SETS) - 1)];
}

if (!empty(ICON_SETS[$iconset])) {
  $icons = ICON_SETS[$iconset];
}
$icons = array_filter(mb_str_split($icons));
$speed_values = $options['speed'] ?? DEFAULT_SPEED_VALUES;
$speed_values = array_filter(explode(',', $speed_values));
$speed_values[] = 0;

$colors = $options['colors'] ?? '';
if (!$colors && !empty(COLOR_SETS[$iconset])) {
  $colors = COLOR_SETS[$iconset];
}
if (!$colors || $colors == 'none') {
  $colors = [];
}
if ($colors && !is_array($colors)) {
  $colors = explode(',', $colors);
}

$host = $options['host'] ?? DEFAULT_HOST;

if (count($icons) != count($speed_values)) {
  exit("The icon set must have one more item than the speed values.\n");
}
if ($colors && count($colors) != count($speed_values)) {
  exit("The colors must have one more item than the speed values.\n");
}

$display_map = [];
foreach ($speed_values as $n => $speed_value) {
  $display_map[$speed_value] = [
    'icon' => $icons[$n],
    'color' => $colors[$n] ?? '',
  ];
}

print format_ping_time(get_latency($host), $display_map);
