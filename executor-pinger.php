<?php

/**
 * See https://getemoji.com/
 */
//const RED = "🔴";
//const YELLOW = "🟡";
//const ORANGE = "🟠";
//const PURPLE = "🟣";
//const BLUE = "🔵";
//const GREEN = "🟢";
const RED = "🟥";
const YELLOW = "🟨";
const PURPLE = "🟪";
const BLUE = "🟦";
const GREEN = "🟩";

const RED_FLAG = "🚩";
const TOMATO = "🍅";
const LEMON = "🍋";
const GRAPES = "🍇";
const BLUEBERRIES = "🫐";
const KIWI = "🥝";
const APPLE = "🍏";

//const SPEED_COLORS = [
//  1000 => RED,
//  120 => YELLOW,
//  60 => PURPLE,
//  30 => BLUE,
//  0 => GREEN,
//];
const SPEED_COLORS = [
  1000 => TOMATO,
  120 => LEMON,
  60 => GRAPES,
  30 => BLUEBERRIES,
  0 => APPLE,
];

$host = '8.8.8.8';

/**
 * Format latency time.
 *
 * @param float $time
 *   Latency n milliseconds.
 *
 * @return string
 *   Formatted latency.
 */
function format_ping_time($time) {
  $color = '';
  if (!$time = round(floatval($time))) {
    $time = '❌';
  }
  else {
    foreach (SPEED_COLORS as $min => $color) {
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

print format_ping_time(get_latency($host));
