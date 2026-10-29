<?php
/** Server-authoritative physical zakat assessment; estimates require Sharia review. */
function physicalAssess(string $type, array $data): array {
    $type = strtolower($type);
    $n = static function($key) use ($data): float {
        $v=$data[$key]??null;
        if (!is_scalar($v) || !is_numeric($v) || !is_finite((float)$v) || (float)$v<0 || (float)$v>1000000000) throw new InvalidArgumentException('Invalid '.$key);
        return (float)$v;
    };
    if ($type==='agriculture') {
        $amount=$n('harvest_amount'); $unit=(string)($data['measurement_unit']??'');
        $weights=['kg'=>1,'bag50'=>50,'bag100'=>100,'ton'=>1000];
        if ($unit==='custom') { $w=$n('custom_weight'); if ($w<=0) throw new InvalidArgumentException('Custom unit weight required'); }
        elseif (isset($weights[$unit])) $w=$weights[$unit];
        else throw new InvalidArgumentException('Choose a valid measurement unit');
        $kg=$amount*$w; if ($kg<=0) throw new InvalidArgumentException('Enter harvest amount');
        $irrigation=(string)($data['irrigation']??'');
        $rates=['rain'=>0.10,'artificial'=>0.05,'mixed'=>0.075];
        if (!isset($rates[$irrigation])) throw new InvalidArgumentException('Choose irrigation method');
        $nisab=609.84; $eligible=$kg >= $nisab;
        return ['type'=>$type,'quantity'=>$kg,'unit'=>'kg','nisab'=>$nisab,'eligible'=>$eligible,'due'=>$eligible?round($kg*$rates[$irrigation],3):0,'due_unit'=>'kg','description'=>$eligible?'Estimated harvest zakat; subject to crop eligibility and Sharia review':'Below configured agricultural nisab','rate'=>$rates[$irrigation],'manual_review'=>true];
    }
    $field=['goats'=>'goats','cows'=>'cows','camels'=>'camels'][$type]??null;
    if (!$field) throw new InvalidArgumentException('Unsupported physical type');
    $count=$n($field); if ($count<=0 || floor($count)!==$count) throw new InvalidArgumentException('Enter a positive whole number of animals');
    $count=(int)$count; $due=0; $desc='Below nisab'; $nisab=['goats'=>40,'cows'=>30,'camels'=>5][$type];
    if ($type==='goats' && $count>=40) {
        $due=$count<=120?1:($count<=200?2:($count<=300?3:(int)floor($count/100)));
        $desc=$due.' sheep/goat(s), age and condition subject to review';
    }
    if ($type==='cows' && $count>=30) {
        // Optimize 30-head tabi and 40-head musinnah groups, leaving minimal remainder.
        $best=null;
        for($a=0;$a<=$count/30;$a++) for($b=0;$b<=$count/40;$b++) {
            $rem=$count-30*$a-40*$b; if($rem<0) continue;
            if($best===null || $rem<$best[0] || ($rem===$best[0] && $a+$b<$best[1]+$best[2])) $best=[$rem,$a,$b];
        }
        $due=$best[1]+$best[2]; $desc=$best[1].' tabi (1-year) + '.$best[2].' musinnah (2-year); remainder '.$best[0];
    }
    if ($type==='camels' && $count>=5) {
        if($count<=24) { $due=(int)floor($count/5); $desc=$due.' sheep/goat(s)'; }
        elseif($count<=35){$due=1;$desc='1 bint makhad';}
        elseif($count<=45){$due=1;$desc='1 bint labun';}
        elseif($count<=60){$due=1;$desc='1 hiqqah';}
        elseif($count<=75){$due=1;$desc='1 jadha';}
        elseif($count<=90){$due=2;$desc='2 bint labun';}
        elseif($count<=120){$due=2;$desc='2 hiqqah';}
        else {
            $best=null;
            for($a=0;$a<=$count/40;$a++) for($b=0;$b<=$count/50;$b++) {
                $rem=$count-40*$a-50*$b; if($rem<0) continue;
                if($best===null || $rem<$best[0]) $best=[$rem,$a,$b];
            }
            $due=$best[1]+$best[2]; $desc=$best[1].' bint labun + '.$best[2].' hiqqah; remainder '.$best[0];
        }
    }
    return ['type'=>$type,'quantity'=>$count,'unit'=>'heads','nisab'=>$nisab,'eligible'=>$count >= $nisab,'due'=>$due,'due_unit'=>'animals','description'=>$desc,'rate'=>null,'manual_review'=>true];
}
