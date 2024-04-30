<?php

/**
 * See https://getemoji.com/
 */
const ICON_SETS = [
  'circles' => '🔴🟡🟣🔵🟢',
  'squares' => '🟥🟨🟪🟦🟩',
  'fruit' => '🍅🍋🍇🫐🍏',
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
  $color = '';
  if (!$time = round(floatval($time))) {
    $time = '❌';
  }
  else {
    foreach ($display_map as $min => $color) {
      if ($time >= $min) {
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
  return trim($color . ' ' . $time);
}

function get_latency($host) {
  $latency = `( ping $host -c 1 | grep -E -o 'time=[0-9.]+' | cut -f2 -d'=') 2>/dev/null`;
  return trim($latency);
}

$options = getopt('', ['icons::', 'speed::', 'host::', 'help::']);

$icons = $options['icons'] ?? 'circles';
if ($icons == 'random') {
  $icons = array_keys(ICON_SETS)[rand(0, count(ICON_SETS) - 1)];
}
if (!empty(ICON_SETS[$icons])) {
  $icons = ICON_SETS[$icons];
}
$icons = array_filter(mb_str_split($icons));
$speed_values = $options['speed'] ?? DEFAULT_SPEED_VALUES;
$speed_values = array_filter(explode(',', $speed_values));
$speed_values[] = 0;

if (count($icons) != count($speed_values)) {
  exit("The icon set must have one more item than the speed values.\n");
}

$display_map = array_combine($speed_values, $icons);
$host = $options['host'] ?? DEFAULT_HOST;

print format_ping_time(get_latency($host), $display_map);
