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

final class EncodedQr
{
    /** @param array<int,array<int,bool>> $modules */
    public function __construct(
        private readonly int $version,
        private readonly int $mask,
        private readonly array $modules
    ) {}

    public function getVersion(): int { return $this->version; }
    public function getMask(): int { return $this->mask; }
    public function getSize(): int { return count($this->modules); }
    /** @return array<int,array<int,bool>> */
    public function getModules(): array { return $this->modules; }
}
