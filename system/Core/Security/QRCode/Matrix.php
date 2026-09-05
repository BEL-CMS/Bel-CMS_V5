<?php
/**
 * Bel-CMS [Content management system]
 * @version 4.2.0 [PHP8.5]
 * @link https://bel-cms.dev
 * @link https://determe.be
 * @license MIT License
 * @copyright 2015-2026 Bel-CMS
 * @author as Stive - stive@determe.be
*/

declare(strict_types=1);
namespace BelCMS\Core\Security\QRCode;

final class Matrix
{
    private int $size;
    /** @var array<int,array<int,bool>> */ private array $modules;
    /** @var array<int,array<int,bool>> */ private array $function;

    private function __construct(private readonly int $version)
    {
        $this->size = 17 + 4 * $version;
        $this->modules = array_fill(0, $this->size, array_fill(0, $this->size, false));
        $this->function = array_fill(0, $this->size, array_fill(0, $this->size, false));
    }

    /** @param int[] $codewords */
    public static function build(int $version, ErrorCorrectionLevel $level, array $codewords): EncodedQr
    {
        $best = null; $bestPenalty = PHP_INT_MAX; $bestMask = 0;
        for ($mask = 0; $mask < 8; $mask++) {
            $m = new self($version);
            $m->drawFunctionPatterns();
            $m->drawCodewords($codewords);
            $m->applyMask($mask);
            $m->drawFormatBits($level, $mask);
            if ($version >= 7) $m->drawVersion();
            $penalty = $m->penalty();
            if ($penalty < $bestPenalty) {
                $bestPenalty = $penalty; $bestMask = $mask; $best = $m->modules;
            }
        }
        return new EncodedQr($version, $bestMask, $best ?? []);
    }

    private function setFunction(int $x, int $y, bool $dark): void
    {
        if ($x < 0 || $y < 0 || $x >= $this->size || $y >= $this->size) return;
        $this->modules[$y][$x] = $dark;
        $this->function[$y][$x] = true;
    }

    private function drawFunctionPatterns(): void
    {
        for ($i = 0; $i < $this->size; $i++) {
            $this->setFunction(6, $i, $i % 2 === 0);
            $this->setFunction($i, 6, $i % 2 === 0);
        }
        $this->drawFinder(3, 3);
        $this->drawFinder($this->size - 4, 3);
        $this->drawFinder(3, $this->size - 4);

        $positions = Capacity::get($this->version)['align'];
        foreach ($positions as $cy) {
            foreach ($positions as $cx) {
                if ($this->function[$cy][$cx]) continue;
                $this->drawAlignment($cx, $cy);
            }
        }

        // Réserve les zones de format.
        for ($i = 0; $i < 9; $i++) {
            if ($i !== 6) {
                $this->setFunction(8, $i, false);
                $this->setFunction($i, 8, false);
            }
        }
        for ($i = 0; $i < 8; $i++) {
            $this->setFunction($this->size - 1 - $i, 8, false);
            $this->setFunction(8, $this->size - 1 - $i, false);
        }
        $this->setFunction(8, $this->size - 8, true); // dark module

        if ($this->version >= 7) {
            for ($i = 0; $i < 18; $i++) {
                $a = $this->size - 11 + ($i % 3);
                $b = intdiv($i, 3);
                $this->setFunction($a, $b, false);
                $this->setFunction($b, $a, false);
            }
        }
    }

    private function drawFinder(int $cx, int $cy): void
    {
        for ($dy = -4; $dy <= 4; $dy++) {
            for ($dx = -4; $dx <= 4; $dx++) {
                $dist = max(abs($dx), abs($dy));
                $this->setFunction($cx + $dx, $cy + $dy, $dist !== 2 && $dist !== 4);
            }
        }
    }

    private function drawAlignment(int $cx, int $cy): void
    {
        for ($dy = -2; $dy <= 2; $dy++) {
            for ($dx = -2; $dx <= 2; $dx++) {
                $this->setFunction($cx + $dx, $cy + $dy, max(abs($dx), abs($dy)) !== 1);
            }
        }
    }

    /** @param int[] $codewords */
    private function drawCodewords(array $codewords): void
    {
        $bits = [];
        foreach ($codewords as $byte) {
            for ($i = 7; $i >= 0; $i--) $bits[] = (($byte >> $i) & 1) !== 0;
        }
        $i = 0;
        for ($right = $this->size - 1; $right >= 1; $right -= 2) {
            if ($right === 6) $right--;
            for ($vert = 0; $vert < $this->size; $vert++) {
                $y = (((($right + 1) & 2) === 0) ? $this->size - 1 - $vert : $vert);
                for ($j = 0; $j < 2; $j++) {
                    $x = $right - $j;
                    if (!$this->function[$y][$x] && $i < count($bits)) {
                        $this->modules[$y][$x] = $bits[$i++];
                    }
                }
            }
        }
        if ($i !== count($bits)) throw new \LogicException('Tous les bits QR n’ont pas été placés.');
    }

    private function applyMask(int $mask): void
    {
        for ($y = 0; $y < $this->size; $y++) {
            for ($x = 0; $x < $this->size; $x++) {
                if ($this->function[$y][$x]) continue;
                $invert = match ($mask) {
                    0 => (($x + $y) % 2) === 0,
                    1 => ($y % 2) === 0,
                    2 => ($x % 3) === 0,
                    3 => (($x + $y) % 3) === 0,
                    4 => ((intdiv($x, 3) + intdiv($y, 2)) % 2) === 0,
                    5 => (($x * $y) % 2 + ($x * $y) % 3) === 0,
                    6 => (((($x * $y) % 2) + (($x * $y) % 3)) % 2) === 0,
                    7 => (((($x + $y) % 2) + (($x * $y) % 3)) % 2) === 0,
                };
                if ($invert) $this->modules[$y][$x] = !$this->modules[$y][$x];
            }
        }
    }

    private function drawFormatBits(ErrorCorrectionLevel $level, int $mask): void
    {
        $data = ($level->formatBits() << 3) | $mask;
        $rem = $data;
        for ($i = 0; $i < 10; $i++) $rem = ($rem << 1) ^ ((($rem >> 9) & 1) * 0x537);
        $bits = (($data << 10) | $rem) ^ 0x5412;
        $get = fn(int $i): bool => (($bits >> $i) & 1) !== 0;

        for ($i = 0; $i <= 5; $i++) $this->setFunction(8, $i, $get($i));
        $this->setFunction(8, 7, $get(6));
        $this->setFunction(8, 8, $get(7));
        $this->setFunction(7, 8, $get(8));
        for ($i = 9; $i < 15; $i++) $this->setFunction(14 - $i, 8, $get($i));

        for ($i = 0; $i < 8; $i++) $this->setFunction($this->size - 1 - $i, 8, $get($i));
        for ($i = 8; $i < 15; $i++) $this->setFunction(8, $this->size - 15 + $i, $get($i));
        $this->setFunction(8, $this->size - 8, true);
    }

    private function drawVersion(): void
    {
        $rem = $this->version;
        for ($i = 0; $i < 12; $i++) $rem = ($rem << 1) ^ ((($rem >> 11) & 1) * 0x1F25);
        $bits = ($this->version << 12) | $rem;
        for ($i = 0; $i < 18; $i++) {
            $bit = (($bits >> $i) & 1) !== 0;
            $a = $this->size - 11 + ($i % 3);
            $b = intdiv($i, 3);
            $this->setFunction($a, $b, $bit);
            $this->setFunction($b, $a, $bit);
        }
    }

    private function penalty(): int
    {
        $p = 0;
        for ($y=0;$y<$this->size;$y++) $p += $this->linePenalty($this->modules[$y]);
        for ($x=0;$x<$this->size;$x++) {
            $line=[]; for($y=0;$y<$this->size;$y++) $line[]=$this->modules[$y][$x];
            $p += $this->linePenalty($line);
        }
        for ($y=0;$y<$this->size-1;$y++) for($x=0;$x<$this->size-1;$x++) {
            $c=$this->modules[$y][$x];
            if($this->modules[$y][$x+1]===$c && $this->modules[$y+1][$x]===$c && $this->modules[$y+1][$x+1]===$c) $p+=3;
        }
        $dark=0; foreach($this->modules as $row) foreach($row as $v) if($v)$dark++;
        $total=$this->size*$this->size;
        $p += intdiv(abs($dark*20-$total*10), $total)*10;
        return $p;
    }

    /** @param bool[] $line */
    private function linePenalty(array $line): int
    {
        $p=0; $runColor=$line[0]; $run=1;
        for($i=1,$n=count($line);$i<$n;$i++) {
            if($line[$i]===$runColor){$run++; if($run===5)$p+=3; elseif($run>5)$p++;}
            else{$runColor=$line[$i];$run=1;}
        }
        $pattern=[true,false,true,true,true,false,true];
        $n=count($line);
        for($i=0;$i<=$n-7;$i++) {
            $match=true; for($j=0;$j<7;$j++) if($line[$i+$j]!==$pattern[$j]){$match=false;break;}
            if(!$match)continue;
            $left=$i>=4 && !$line[$i-1]&&!$line[$i-2]&&!$line[$i-3]&&!$line[$i-4];
            $right=$i+10<$n && !$line[$i+7]&&!$line[$i+8]&&!$line[$i+9]&&!$line[$i+10];
            if($left||$right)$p+=40;
        }
        return $p;
    }
}
