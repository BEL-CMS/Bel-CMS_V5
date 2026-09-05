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
namespace BelCMS\Core\Security\QRCode\Renderer;
use BelCMS\Core\Security\QRCode\EncodedQr;

final class PngRenderer
{
    public static function render(EncodedQr $qr, int $pixels=350, int $margin=4): string
    {
        if(!extension_loaded('gd')) throw new \RuntimeException('L’extension GD est nécessaire pour le rendu PNG.');
        $n=$qr->getSize(); $total=$n+2*$margin; $scale=max(1,intdiv($pixels,$total)); $actual=$scale*$total;
        $img=imagecreatetruecolor($actual,$actual); if($img===false) throw new \RuntimeException('Création PNG impossible.');
        $white=imagecolorallocate($img,255,255,255); $black=imagecolorallocate($img,0,0,0); imagefill($img,0,0,$white);
        foreach($qr->getModules() as $y=>$row) foreach($row as $x=>$dark) if($dark) imagefilledrectangle($img,($x+$margin)*$scale,($y+$margin)*$scale,($x+$margin+1)*$scale-1,($y+$margin+1)*$scale-1,$black);
        ob_start(); imagepng($img); $png=(string)ob_get_clean(); imagedestroy($img); return $png;
    }
}
