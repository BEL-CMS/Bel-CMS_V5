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

final class SvgRenderer
{
    public static function render(EncodedQr $qr, int $pixels=350, int $margin=4): string
    {
        $n=$qr->getSize(); $view=$n+2*$margin; $path='';
        foreach($qr->getModules() as $y=>$row) foreach($row as $x=>$dark) if($dark) $path.='M'.($x+$margin).','.($y+$margin).'h1v1h-1z';
        return '<svg xmlns="http://www.w3.org/2000/svg" width="'.$pixels.'" height="'.$pixels.'" viewBox="0 0 '.$view.' '.$view.'" shape-rendering="crispEdges" role="img" aria-label="QR Code"><rect width="100%" height="100%" fill="#fff"/><path d="'.$path.'" fill="#000"/></svg>';
    }
}
