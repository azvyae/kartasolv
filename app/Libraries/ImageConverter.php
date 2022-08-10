<?php

namespace App\Libraries;

use CodeIgniter\Files\File;

class ImageConverter
{

    /**
     * Convert to webp and compress
     *
     * @param	object	$file
     * @return	File
     */
    public function convertToWebp($filePath, int $maxSize = 0)
    {
        $file = new File($filePath, true);
        $file->getFilename();
        if ($file->getMimeType() == 'image/webp' && $file->getSize() < ($maxSize * 1024)) {
            return TRUE;
        }
        $quality = 80;

        $info = getimagesize($file);
        $img = \Config\Services::image('');
        $img->withFile($file);
        if ($info[0] > 2048 or $info[1] > 2048) {
            $img->resize(2048, 2048, true);
        }
        $newFile = str_replace('.' . $file->getExtension(), '.webp', $filePath);
        $img->convert(IMAGETYPE_WEBP);
        $img->save($newFile, $quality);
        return $newFile;
    }
}
