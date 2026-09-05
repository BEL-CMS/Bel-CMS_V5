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

final class HtmlRenderer
{
    public static function render(EncodedQr $qr, int $pixels=350, int $margin=4): string
    {
        $n=$qr->getSize(); $total=$n+2*$margin; $cell=$pixels/$total;
        $html='<div style="display:grid;grid-template-columns:repeat('.$total.','.$cell.'px);width:'.$pixels.'px;height:'.$pixels.'px;background:#fff">';
        for($y=-$margin;$y<$n+$margin;$y++) for($x=-$margin;$x<$n+$margin;$x++) {
            $dark=$x>=0&&$y>=0&&$x<$n&&$y<$n&&$qr->getModules()[$y][$x];
            $html.='<span style="background:'.($dark?'#000':'#fff').'"></span>';
        }
        return $html.'</div>';
    }
}
