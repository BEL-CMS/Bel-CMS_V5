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
namespace BelCMS\Core\Security\TOTP;
final class ProvisioningUri
{
    public static function create(string $secret,string $account,string $issuer): string
    {
        $label=rawurlencode($issuer).':'.rawurlencode($account);
        return 'otpauth://totp/'.$label.'?secret='.rawurlencode($secret).'&issuer='.rawurlencode($issuer);
    }
}