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

final class Capacity
{
    /** @var array<int,array{size:int,data:int,total:int,blocks:array<int,array{count:int,data:int,ecc:int}>,align:int[]}> */
    private const TABLE = [
        1  => ['size'=>21,'data'=>19,'total'=>26,'blocks'=>[['count'=>1,'data'=>19,'ecc'=>7]],'align'=>[]],
        2  => ['size'=>25,'data'=>34,'total'=>44,'blocks'=>[['count'=>1,'data'=>34,'ecc'=>10]],'align'=>[6,18]],
        3  => ['size'=>29,'data'=>55,'total'=>70,'blocks'=>[['count'=>1,'data'=>55,'ecc'=>15]],'align'=>[6,22]],
        4  => ['size'=>33,'data'=>80,'total'=>100,'blocks'=>[['count'=>1,'data'=>80,'ecc'=>20]],'align'=>[6,26]],
        5  => ['size'=>37,'data'=>108,'total'=>134,'blocks'=>[['count'=>1,'data'=>108,'ecc'=>26]],'align'=>[6,30]],
        6  => ['size'=>41,'data'=>136,'total'=>172,'blocks'=>[['count'=>2,'data'=>68,'ecc'=>18]],'align'=>[6,34]],
        7  => ['size'=>45,'data'=>156,'total'=>196,'blocks'=>[['count'=>2,'data'=>78,'ecc'=>20]],'align'=>[6,22,38]],
        8  => ['size'=>49,'data'=>194,'total'=>242,'blocks'=>[['count'=>2,'data'=>97,'ecc'=>24]],'align'=>[6,24,42]],
        9  => ['size'=>53,'data'=>232,'total'=>292,'blocks'=>[['count'=>2,'data'=>116,'ecc'=>30]],'align'=>[6,26,46]],
        10 => ['size'=>57,'data'=>274,'total'=>346,'blocks'=>[['count'=>2,'data'=>68,'ecc'=>18],['count'=>2,'data'=>69,'ecc'=>18]],'align'=>[6,28,50]],
    ];

    public static function get(int $version): array
    {
        if (!isset(self::TABLE[$version])) {
            throw new \InvalidArgumentException('Version QR prise en charge : 1 à 10.');
        }
        return self::TABLE[$version];
    }

    public static function selectVersion(int $payloadBytes): int
    {
        for ($version = 1; $version <= 10; $version++) {
            $charCountBits = $version <= 9 ? 8 : 16;
            $requiredBits = 4 + $charCountBits + ($payloadBytes * 8);
            if ($requiredBits <= self::TABLE[$version]['data'] * 8) {
                return $version;
            }
        }
        throw new \LengthException('Le contenu nécessite plus de capacité que la version 10-L.');
    }
}
