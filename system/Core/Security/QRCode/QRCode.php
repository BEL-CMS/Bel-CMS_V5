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

use BelCMS\Core\Security\QRCode\Renderer\SvgRenderer;
use BelCMS\Core\Security\QRCode\Renderer\HtmlRenderer;
use BelCMS\Core\Security\QRCode\Renderer\PngRenderer;

final class QRCode
{
    private ErrorCorrectionLevel $level = ErrorCorrectionLevel::L;
    private int $size = 350;
    private int $margin = 4;
    private ?int $version = null;

    private function __construct(private readonly string $text) {}

    public static function make(string $text): self { return new self($text); }
    public function level(ErrorCorrectionLevel $level): self { $clone=clone $this; $clone->level=$level; return $clone; }
    public function size(int $size): self { if($size<1) throw new \InvalidArgumentException('Taille invalide.'); $clone=clone $this; $clone->size=$size; return $clone; }
    public function margin(int $margin): self { if($margin<0) throw new \InvalidArgumentException('Marge invalide.'); $clone=clone $this; $clone->margin=$margin; return $clone; }
    public function version(?int $version): self { if($version!==null && ($version<1||$version>10)) throw new \InvalidArgumentException('Version 1 à 10.'); $clone=clone $this; $clone->version=$version; return $clone; }
    public function encode(): EncodedQr { return Encoder::encode($this->text,$this->level,$this->version); }
    public function svg(): string { return SvgRenderer::render($this->encode(),$this->size,$this->margin); }
    public function html(): string { return HtmlRenderer::render($this->encode(),$this->size,$this->margin); }
    public function png(): string { return PngRenderer::render($this->encode(),$this->size,$this->margin); }
}
