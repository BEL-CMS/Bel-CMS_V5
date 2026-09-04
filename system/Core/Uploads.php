<?php
/**
 * Bel-CMS [Content management system]
 * @version 5.0.0 [PHP8.5]
 * @link https://bel-cms.dev
 * @link https://determe.be
 * @license Apache-2.0 license
 * @copyright 2015-2026 Bel-CMS
 * @author as Stive - stive@determe.be
*/

declare(strict_types=1);

namespace BelCMS\Core;

use BelCMS\system\Common;

/*
####### Utilisation infos #########

$upload = BelcmsUpload::file('avatar', 'uploads/avatar')
    ->images()
    ->randomName()
    ->upload();

####### Tous les fichiers : ####### 

$upload = BelcmsUpload::file('document', 'uploads/files')
    ->upload();

####### Extensions personnalisées : ####### 

$upload = BelcmsUpload::file('archive', 'uploads')
    ->extensions(['zip', 'rar'])
    ->upload();
*/

class BelcmsUpload
{
    private string $input;
    private string $directory;
    private bool $randomName = false;
    private array $extensions = [];
    private ?int $resizeWidth = null;
    private ?int $resizeHeight = null;
    private bool $keepRatio = true;
    private ?string $watermarkText = null;
    private int $watermarkSize = 20;
    private int $watermarkOpacity = 50;
    private string $watermarkPosition = 'bottom-right';

    private int $quality = 90;
    private const IMAGES = [
        'jpg','jpeg','png','gif','bmp','webp','svg','ico',
        'tif','tiff','heic','jpe'
    ];

    private const ALL = [
        'jpg','jpeg','png','gif','bmp','webp','svg','ico',
        'tif','tiff','heic','jpe',
        'pdf','doc','docx','xls','xlsx','ppt','pptx',
        'zip','rar','7z','tar',
        'txt','xml','nfo',
        'mp3','mp4','avi','mpeg','mpg',
        'apk','jar','iso'
    ];

    private function __construct(string $input, string $directory)
    {
        $this->input = $input;
        $this->directory = rtrim($directory, '/');
        $this->extensions = self::ALL;
    }

    public static function file(string $input, string $directory): self
    {
        return new self($input, $directory);
    }

    public function resize(int $width, int $height, bool $keepRatio = true): self
    {
        $this->resizeWidth  = $width;
        $this->resizeHeight = $height;
        $this->keepRatio    = $keepRatio;

        return $this;
    }

    public function quality(int $quality): self
    {
        $this->quality = max(0, min(100, $quality));

        return $this;
    }

    public function images(): self
    {
        $this->extensions = self::IMAGES;
        return $this;
    }

    public function extensions(array $extensions): self
    {
        $this->extensions = array_map('strtolower', $extensions);
        return $this;
    }

    public function randomName(bool $value = true): self
    {
        $this->randomName = $value;
        return $this;
    }

    public function upload(): array
    {
        if (!isset($_FILES[$this->input])) {
            return [
                'success' => false,
                'error'   => 'UPLOAD_NONE'
            ];
        }

        $file = $_FILES[$this->input];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return [
                'success' => false,
                'error'   => 'UPLOAD_ERROR'
            ];
        }

        if (!is_dir($this->directory)) {
            mkdir($this->directory, 0775, true);
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $this->extensions, true)) {
            return [
                'success' => false,
                'error'   => 'UPLOAD_ERROR_FILE'
            ];
        }

        if ($file['size'] > Common::GetMaximumFileUploadSize()) {
            return [
                'success' => false,
                'error'   => 'UPLOAD_ERROR_SIZE'
            ];
        }

        $name = Common::cleanFileName($file['name']);

        if ($this->randomName) {

            $info = pathinfo($name);

            $name = Common::randomString(16)
                  . '-'
                  . $info['filename']
                  . '.'
                  . $info['extension'];
        }

        $destination = $this->directory.'/'.$name;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return [
                'success' => false,
                'error'   => 'UPLOAD_ERROR'
            ];
        }

        if ($this->resizeWidth !== null) {
            $this->resizeImage($destination);
        }

        if ($this->watermarkText !== null) {
            $this->addTextWatermark($destination);
        }

        return [
            'success' => true,
            'file'    => '/'.$name,
            'filename'=> $name,
            'path'    => $destination,
            'size'    => filesize($destination),
            'mime'    => mime_content_type($destination)
        ];
    }
    private function resizeImage(string $file): void
    {
        [$width, $height, $type] = getimagesize($file);

        switch ($type) {
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($file);
                break;

            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($file);
                break;

            case IMAGETYPE_WEBP:
                $source = imagecreatefromwebp($file);
                break;

            case IMAGETYPE_GIF:
                $source = imagecreatefromgif($file);
                break;

            default:
                return;
        }

        if ($this->keepRatio) {

            $ratio = min(
                $this->resizeWidth / $width,
                $this->resizeHeight / $height
            );

            $newWidth  = (int)($width * $ratio);
            $newHeight = (int)($height * $ratio);

        } else {

            $newWidth  = $this->resizeWidth;
            $newHeight = $this->resizeHeight;
        }

        $image = imagecreatetruecolor($newWidth, $newHeight);

        imagealphablending($image, false);
        imagesavealpha($image, true);

        imagecopyresampled(
            $image,
            $source,
            0,
            0,
            0,
            0,
            $newWidth,
            $newHeight,
            $width,
            $height
        );

        switch ($type) {

            case IMAGETYPE_JPEG:
                imagejpeg($image, $file, $this->quality);
                break;

            case IMAGETYPE_PNG:
                imagepng($image, $file);
                break;

            case IMAGETYPE_WEBP:
                imagewebp($image, $file, $this->quality);
                break;

            case IMAGETYPE_GIF:
                imagegif($image, $file);
                break;
        }

        $source = null;
        $image  = null;
    }

    private function addTextWatermark(string $file): void
    {
        [$width, $height, $type] = getimagesize($file);

        switch ($type) {

            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($file);
                break;

            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($file);
                break;

            case IMAGETYPE_WEBP:
                $image = imagecreatefromwebp($file);
                break;

            default:
                return;
        }


        $alpha = (int) round(
            127 - (127 * ($this->watermarkOpacity / 100))
        );

        $alpha = max(0, min(127, $alpha));

        $color = imagecolorallocatealpha(
            $image,
            255,
            255,
            255,
            $alpha
        );


        $font = 5; // police GD intégrée


        $textWidth = imagefontwidth($font) * strlen($this->watermarkText);
        $textHeight = imagefontheight($font);


        switch ($this->watermarkPosition) {

            case 'top-left':
                $x = 10;
                $y = 10;
                break;


            case 'top-right':
                $x = $width - $textWidth - 10;
                $y = 10;
                break;


            case 'bottom-left':
                $x = 10;
                $y = $height - $textHeight - 10;
                break;


            case 'center':
                $x = ($width - $textWidth) / 2;
                $y = ($height - $textHeight) / 2;
                break;


            default:
                $x = $width - $textWidth - 10;
                $y = $height - $textHeight - 10;
        }


        imagestring(
            $image,
            $font,
            $x,
            $y,
            $this->watermarkText,
            $color
        );


        switch ($type) {

            case IMAGETYPE_JPEG:
                imagejpeg($image, $file, 90);
                break;

            case IMAGETYPE_PNG:
                imagepng($image, $file);
                break;

            case IMAGETYPE_WEBP:
                imagewebp($image, $file, 90);
                break;
        }


        $image = null;
    }

    public function watermarkText(
        string $text,
        int $size = 20,
        int $opacity = 50,
        string $position = 'bottom-right'
    ): self {

        $this->watermarkText = $text;
        $this->watermarkSize = $size;
        $this->watermarkOpacity = $opacity;
        $this->watermarkPosition = $position;

        return $this;
    }
}