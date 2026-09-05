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

final class BitBuffer
{
    /** @var int[] */
    private array $bits = [];

    public function append(int $value, int $length): void
    {
        if ($length < 0 || ($length < 31 && ($value >> $length) !== 0)) {
            throw new \InvalidArgumentException('Valeur impossible à encoder sur ce nombre de bits.');
        }
        for ($i = $length - 1; $i >= 0; $i--) {
            $this->bits[] = ($value >> $i) & 1;
        }
    }

    public function appendBytes(string $data): void
    {
        foreach (unpack('C*', $data) ?: [] as $byte) {
            $this->append($byte, 8);
        }
    }

    public function length(): int { return count($this->bits); }
    /** @return int[] */
    public function bits(): array { return $this->bits; }

    /** @return int[] */
    public function toBytes(): array
    {
        if (($this->length() % 8) !== 0) {
            throw new \LogicException('Le flux binaire doit être aligné sur un octet.');
        }
        $out = [];
        for ($i = 0; $i < $this->length(); $i += 8) {
            $value = 0;
            for ($j = 0; $j < 8; $j++) {
                $value = ($value << 1) | $this->bits[$i + $j];
            }
            $out[] = $value;
        }
        return $out;
    }
}
