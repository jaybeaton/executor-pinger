#!/bin/bash

HOST="8.8.8.8";
ICONS=(🍅 🍋 🍇 🫐 🍏);
COLORS=('#ff3343' '#ffc855' '#8f5ca0' '#5e79cc' '#97ca69');
SPEED_VALUES=(1000 120 60 30);
SPEED_VALUES+=(0)

LATENCY=`( ping $HOST -c 1 -w 3 | grep -E -o 'time=[0-9.]+' | cut -f2 -d'=') 2>/dev/null`;
LATENCY=$(printf '%.0f' "$LATENCY");

if [[ "$LATENCY" -eq "0" ]]; then
  echo "❌";
  exit;
fi

for i in "${!SPEED_VALUES[@]}";
do
  MIN="${SPEED_VALUES[$i]}"
  if [[ "$LATENCY" -ge "$MIN" ]]; then
    ICON="${ICONS[$i]}";
    COLOR="${COLORS[$i]}";
    break;
  fi
done

if [[ "$LATENCY" -ge 1000 ]]; then
  LATENCY=$(echo "scale=1 ; $LATENCY / 1000" | bc);
  LATENCY="${LATENCY}s";
else
  LATENCY="${LATENCY}ms";
fi

if [[ -z "${COLOR}" ]]; then
  COLOR="#fff";
fi

echo "<executor.markup.true> <span foreground='$COLOR'><b>$ICON$LATENCY</b></span>";
