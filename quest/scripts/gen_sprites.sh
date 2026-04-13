#!/bin/bash
BASE="d:/Nextcloud/Projects/Nextcloud Quest/quest/img"

declare -A AC
AC[stone]="#8b7355"; AC[bronze]="#cd7f32"; AC[iron]="#71706e"; AC[medieval]="#8b4513"
AC[renaissance]="#daa520"; AC[industrial]="#696969"; AC[modern]="#2c3e50"; AC[digital]="#00ced1"; AC[space]="#9370db"

declare -A RC
RC[common]="#9e9e9e"; RC[rare]="#2196f3"; RC[epic]="#9c27b0"; RC[legendary]="#ff9800"

mk() { local f=$1 s=$2 t=$3 file=$4
case "$t" in
sword) echo "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 64 64\"><rect x=\"30\" y=\"8\" width=\"4\" height=\"32\" rx=\"1\" fill=\"$s\"/><polygon points=\"28,8 32,2 36,8\" fill=\"$s\"/><rect x=\"22\" y=\"38\" width=\"20\" height=\"5\" rx=\"2\" fill=\"$f\" stroke=\"$s\" stroke-width=\"1\"/><rect x=\"30\" y=\"43\" width=\"4\" height=\"10\" rx=\"1\" fill=\"$f\"/></svg>" > "$file" ;;
axe) echo "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 64 64\"><rect x=\"30\" y=\"14\" width=\"4\" height=\"38\" rx=\"1\" fill=\"#8B4513\"/><path d=\"M34 14 Q48 20 42 34 L34 30 Z\" fill=\"$f\" stroke=\"$s\" stroke-width=\"1.5\"/></svg>" > "$file" ;;
spear) echo "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 64 64\"><rect x=\"30\" y=\"18\" width=\"4\" height=\"40\" rx=\"1\" fill=\"#8B4513\"/><polygon points=\"27,18 32,4 37,18\" fill=\"$f\" stroke=\"$s\" stroke-width=\"1\"/></svg>" > "$file" ;;
club) echo "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 64 64\"><rect x=\"29\" y=\"24\" width=\"6\" height=\"30\" rx=\"2\" fill=\"#6b4423\"/><ellipse cx=\"32\" cy=\"18\" rx=\"10\" ry=\"12\" fill=\"$f\" stroke=\"$s\" stroke-width=\"1.5\"/></svg>" > "$file" ;;
gun) echo "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 64 64\"><rect x=\"10\" y=\"24\" width=\"34\" height=\"8\" rx=\"2\" fill=\"$f\" stroke=\"$s\" stroke-width=\"1\"/><rect x=\"24\" y=\"32\" width=\"10\" height=\"14\" rx=\"2\" fill=\"$f\" stroke=\"$s\" stroke-width=\"1\"/><rect x=\"8\" y=\"26\" width=\"6\" height=\"4\" rx=\"1\" fill=\"$s\"/></svg>" > "$file" ;;
staff) echo "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 64 64\"><rect x=\"30\" y=\"12\" width=\"4\" height=\"44\" rx=\"1\" fill=\"#8B4513\"/><circle cx=\"32\" cy=\"10\" r=\"7\" fill=\"$f\" stroke=\"$s\" stroke-width=\"1.5\"/><circle cx=\"32\" cy=\"10\" r=\"3\" fill=\"$s\" opacity=\"0.5\"/></svg>" > "$file" ;;
dagger) echo "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 64 64\"><rect x=\"30\" y=\"12\" width=\"4\" height=\"24\" rx=\"1\" fill=\"$s\"/><polygon points=\"29,12 32,4 35,12\" fill=\"$s\"/><rect x=\"24\" y=\"34\" width=\"16\" height=\"4\" rx=\"2\" fill=\"$f\"/><rect x=\"30\" y=\"38\" width=\"4\" height=\"10\" rx=\"1\" fill=\"$f\"/></svg>" > "$file" ;;
rapier) echo "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 64 64\"><rect x=\"31\" y=\"6\" width=\"2\" height=\"36\" fill=\"$s\"/><polygon points=\"30,6 32,2 34,6\" fill=\"$s\"/><circle cx=\"32\" cy=\"42\" r=\"8\" fill=\"none\" stroke=\"$f\" stroke-width=\"2\"/><rect x=\"30\" y=\"48\" width=\"4\" height=\"10\" rx=\"1\" fill=\"$f\"/></svg>" > "$file" ;;
tunic) echo "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 64 64\"><path d=\"M18 16 L26 10 L32 14 L38 10 L46 16 L42 24 L38 20 L38 52 L26 52 L26 20 L22 24 Z\" fill=\"$f\" stroke=\"$s\" stroke-width=\"1.5\"/></svg>" > "$file" ;;
armor) echo "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 64 64\"><path d=\"M16 16 L24 8 L32 12 L40 8 L48 16 L44 26 L48 28 L48 50 L16 50 L16 28 L20 26 Z\" fill=\"$f\" stroke=\"$s\" stroke-width=\"1.5\"/><path d=\"M28 18 L32 14 L36 18 L36 30 L28 30 Z\" fill=\"$s\" opacity=\"0.2\"/></svg>" > "$file" ;;
robe) echo "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 64 64\"><path d=\"M18 12 L26 8 L32 10 L38 8 L46 12 L44 20 L46 22 L42 56 L22 56 L18 22 L20 20 Z\" fill=\"$f\" stroke=\"$s\" stroke-width=\"1.5\"/></svg>" > "$file" ;;
shield) echo "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 64 64\"><path d=\"M14 12 L50 12 L50 36 Q32 56 14 36 Z\" fill=\"$f\" stroke=\"$s\" stroke-width=\"2\"/><circle cx=\"32\" cy=\"28\" r=\"4\" fill=\"$s\" opacity=\"0.3\"/></svg>" > "$file" ;;
ring) echo "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 64 64\"><circle cx=\"32\" cy=\"32\" r=\"16\" fill=\"none\" stroke=\"$f\" stroke-width=\"5\"/><circle cx=\"32\" cy=\"16\" r=\"5\" fill=\"$s\"/></svg>" > "$file" ;;
bracelet) echo "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 64 64\"><ellipse cx=\"32\" cy=\"32\" rx=\"18\" ry=\"12\" fill=\"none\" stroke=\"$f\" stroke-width=\"4\"/><circle cx=\"32\" cy=\"20\" r=\"3\" fill=\"$s\"/><circle cx=\"22\" cy=\"26\" r=\"2\" fill=\"$s\" opacity=\"0.6\"/><circle cx=\"42\" cy=\"26\" r=\"2\" fill=\"$s\" opacity=\"0.6\"/></svg>" > "$file" ;;
watch) echo "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 64 64\"><circle cx=\"32\" cy=\"32\" r=\"14\" fill=\"$f\" stroke=\"$s\" stroke-width=\"2\"/><circle cx=\"32\" cy=\"32\" r=\"11\" fill=\"#f5f5dc\"/><line x1=\"32\" y1=\"32\" x2=\"32\" y2=\"22\" stroke=\"#333\" stroke-width=\"1.5\"/><line x1=\"32\" y1=\"32\" x2=\"38\" y2=\"30\" stroke=\"#333\" stroke-width=\"1\"/></svg>" > "$file" ;;
phone) echo "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 64 64\"><rect x=\"20\" y=\"8\" width=\"24\" height=\"48\" rx=\"4\" fill=\"#222\" stroke=\"$s\" stroke-width=\"1.5\"/><rect x=\"22\" y=\"14\" width=\"20\" height=\"32\" rx=\"1\" fill=\"$f\"/><circle cx=\"32\" cy=\"52\" r=\"2.5\" fill=\"#444\"/></svg>" > "$file" ;;
jetpack) echo "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 64 64\"><rect x=\"20\" y=\"10\" width=\"10\" height=\"30\" rx=\"3\" fill=\"$f\" stroke=\"$s\" stroke-width=\"1.5\"/><rect x=\"34\" y=\"10\" width=\"10\" height=\"30\" rx=\"3\" fill=\"$f\" stroke=\"$s\" stroke-width=\"1.5\"/><path d=\"M22 40 L18 56 L28 50 Z\" fill=\"#ff4500\" opacity=\"0.8\"/><path d=\"M36 40 L32 56 L42 50 Z\" fill=\"#ff4500\" opacity=\"0.8\"/></svg>" > "$file" ;;
orb) echo "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 64 64\"><circle cx=\"32\" cy=\"30\" r=\"16\" fill=\"$f\" stroke=\"$s\" stroke-width=\"2\"/><circle cx=\"32\" cy=\"30\" r=\"10\" fill=\"$s\" opacity=\"0.2\"/><circle cx=\"26\" cy=\"24\" r=\"4\" fill=\"#fff\" opacity=\"0.3\"/></svg>" > "$file" ;;
necklace) echo "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 64 64\"><path d=\"M20 18 Q32 38 44 18\" fill=\"none\" stroke=\"$f\" stroke-width=\"2.5\"/><circle cx=\"32\" cy=\"36\" r=\"6\" fill=\"$f\" stroke=\"$s\" stroke-width=\"1.5\"/><circle cx=\"32\" cy=\"36\" r=\"3\" fill=\"$s\" opacity=\"0.5\"/></svg>" > "$file" ;;
helmet) echo "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 64 64\"><path d=\"M12 38 L16 14 Q32 4 48 14 L52 38 Z\" fill=\"$f\" stroke=\"$s\" stroke-width=\"1.5\"/><rect x=\"10\" y=\"36\" width=\"44\" height=\"6\" rx=\"2\" fill=\"$s\" opacity=\"0.5\"/></svg>" > "$file" ;;
crown) echo "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 64 64\"><path d=\"M12 40 L18 16 L26 28 L32 12 L38 28 L46 16 L52 40 Z\" fill=\"$f\" stroke=\"$s\" stroke-width=\"1.5\"/><circle cx=\"20\" cy=\"24\" r=\"2.5\" fill=\"#e74c3c\"/><circle cx=\"32\" cy=\"16\" r=\"2.5\" fill=\"#3498db\"/><circle cx=\"44\" cy=\"24\" r=\"2.5\" fill=\"#2ecc71\"/></svg>" > "$file" ;;
hat) echo "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 64 64\"><ellipse cx=\"32\" cy=\"40\" rx=\"28\" ry=\"6\" fill=\"$f\" stroke=\"$s\" stroke-width=\"1.5\"/><path d=\"M20 40 Q20 16 32 12 Q44 16 44 40\" fill=\"$f\" stroke=\"$s\" stroke-width=\"1.5\"/></svg>" > "$file" ;;
goggles) echo "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 64 64\"><rect x=\"8\" y=\"24\" width=\"48\" height=\"4\" rx=\"2\" fill=\"$f\"/><circle cx=\"22\" cy=\"32\" r=\"10\" fill=\"$f\" stroke=\"$s\" stroke-width=\"1.5\"/><circle cx=\"42\" cy=\"32\" r=\"10\" fill=\"$f\" stroke=\"$s\" stroke-width=\"1.5\"/><circle cx=\"22\" cy=\"32\" r=\"6\" fill=\"#88ccff\" opacity=\"0.7\"/><circle cx=\"42\" cy=\"32\" r=\"6\" fill=\"#88ccff\" opacity=\"0.7\"/></svg>" > "$file" ;;
hood) echo "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 64 64\"><path d=\"M12 44 Q12 8 32 6 Q52 8 52 44\" fill=\"$f\" stroke=\"$s\" stroke-width=\"1.5\"/><ellipse cx=\"32\" cy=\"44\" rx=\"22\" ry=\"4\" fill=\"$f\" stroke=\"$s\" stroke-width=\"1\"/></svg>" > "$file" ;;
headband) echo "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 64 64\"><path d=\"M10 30 Q32 20 54 30\" fill=\"none\" stroke=\"$f\" stroke-width=\"5\"/><circle cx=\"32\" cy=\"24\" r=\"3\" fill=\"$s\"/></svg>" > "$file" ;;
visor) echo "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 64 64\"><path d=\"M10 26 L54 26 L50 38 L14 38 Z\" fill=\"$f\" stroke=\"$s\" stroke-width=\"1.5\"/><rect x=\"16\" y=\"28\" width=\"32\" height=\"6\" rx=\"1\" fill=\"#88ccff\" opacity=\"0.6\"/></svg>" > "$file" ;;
esac
}

count=0
while IFS='|' read -r key type age rarity spath; do
  [ -z "$key" ] && continue
  f="${AC[$age]:-#888}"; s="${RC[$rarity]:-#9e9e9e}"
  dir="$BASE/$(dirname "$spath")"; file="$BASE/$spath"; mkdir -p "$dir"

  shape=""
  case "$type" in
    weapon) case "$key" in *axe*|*battle*) shape=axe;; *spear*) shape=spear;; *club*) shape=club;; *mace*) shape=club;; *pistol*|*rifle*|*musket*|*revolver*|*blaster*|*cannon*) shape=gun;; *staff*|*quill*) shape=staff;; *dagger*) shape=dagger;; *rapier*) shape=rapier;; *) shape=sword;; esac;;
    clothing) case "$key" in *armor*|*plate*|*chainmail*|*knight*|*tactical*|*cyber*|*exo*|*gear*) shape=armor;; *robe*|*scholar*) shape=robe;; *) shape=tunic;; esac;;
    accessory) case "$key" in *shield*|*banner*) shape=shield;; *ring*) shape=ring;; *bracelet*) shape=bracelet;; *watch*) shape=watch;; *phone*|*smartphone*) shape=phone;; *jetpack*) shape=jetpack;; *quantum*|*neural*|*interface*) shape=orb;; *) shape=necklace;; esac;;
    headgear) case "$key" in *crown*) shape=crown;; *hat*|*cap*) shape=hat;; *goggles*) shape=goggles;; *hood*) shape=hood;; *headband*) shape=headband;; *visor*|*headset*) shape=visor;; *) shape=helmet;; esac;;
  esac

  mk "$f" "$s" "$shape" "$file"
  count=$((count + 1))
done
echo "Generated $count sprites"
