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

final class Encoder
{
    public static function encode(string $text, ErrorCorrectionLevel $level = ErrorCorrectionLevel::L, ?int $version = null): EncodedQr
    {
        if ($level !== ErrorCorrectionLevel::L) {
            throw new \InvalidArgumentException('Cette édition prend en charge le niveau L.');
        }
        $payloadLength = strlen($text);
        $version ??= Capacity::selectVersion($payloadLength);
        $cfg = Capacity::get($version);
        $charCountBits = $version <= 9 ? 8 : 16;
        if ($payloadLength >= (1 << $charCountBits)) {
            throw new \LengthException('Texte trop long pour le compteur de caractères de cette version.');
        }

        $bb = new BitBuffer();
        $bb->append(0b0100, 4);
        $bb->append($payloadLength, $charCountBits);
        $bb->appendBytes($text);
        $capacityBits = $cfg['data'] * 8;
        if ($bb->length() > $capacityBits) {
            throw new \LengthException("Le contenu nécessite plus de capacité que la version {$version}-L.");
        }
        $bb->append(0, min(4, $capacityBits - $bb->length()));
        $bb->append(0, (8 - ($bb->length() % 8)) % 8);
        $data = $bb->toBytes();
        for ($pad = 0xEC; count($data) < $cfg['data']; $pad ^= 0xEC ^ 0x11) {
            $data[] = $pad;
        }

        $blocks = [];
        $offset = 0;
        foreach ($cfg['blocks'] as $group) {
            for ($i = 0; $i < $group['count']; $i++) {
                $blockData = array_slice($data, $offset, $group['data']);
                $offset += $group['data'];
                $blocks[] = ['data'=>$blockData, 'ecc'=>ReedSolomon::compute($blockData, $group['ecc'])];
            }
        }
        $codewords = [];
        $maxData = max(array_map(fn(array $b): int => count($b['data']), $blocks));
        for ($i = 0; $i < $maxData; $i++) {
            foreach ($blocks as $block) {
                if (isset($block['data'][$i])) $codewords[] = $block['data'][$i];
            }
        }
        $eccLen = count($blocks[0]['ecc']);
        for ($i = 0; $i < $eccLen; $i++) {
            foreach ($blocks as $block) $codewords[] = $block['ecc'][$i];
        }
        return Matrix::build($version, $level, $codewords);
    }
}
