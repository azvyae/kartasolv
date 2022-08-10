<?php

namespace App\Libraries;

use CodeIgniter\Files\File;

class ImageUploader
{

    public function upload(array $options, File $file = null)
    {
        if (!$options['upload_path']) {
            $options['upload_path'] = '/';
        }
        if ($options['upload_path'][0] !== '/') {
            $options['upload_path'] = '/' . $options['upload_path'];
        }
        if ($options['upload_path'][-1] !== '/') {
            $options['upload_path'] =  $options['upload_path'] . '/';
        }
        $request = service('request');
        if ($options['multi'] ?? false) {
            $imgs = $request->getFiles()[$options['name']];
            $opt = [
                'private' => $options['private'] ?? false,
                'upload_path' => $options['upload_path'],
                'max_size' => $options['max_size'],
                'name' => $options['name'],
            ];
            $images = array_map(function ($e) use ($opt) {
                return $this->upload($opt, $e);
            }, $imgs);
            return $images;
        } else {
            if ($file) {
                $img = $file;
            } else {
                $img = $request->getFile($options['name']);
            }
            if (!$img->isValid()) {
                return FALSE;
            }
            $unConverted = WRITEPATH . 'uploads' . $img->store($options['upload_path'] ?? '', $img->getRandomName());
            $filepath = $this->convertToWebp($unConverted, 300);
            if (!$filepath) {
                return FALSE;
            }
            $file = new File($filepath);
            if ((!($options['private'] ?? false))) {
                if (!is_dir(ROOTPATH . 'public_html/uploads' . $options['upload_path'])) {
                    mkdir(ROOTPATH . 'public_html/uploads' . $options['upload_path'], 0777, true);
                };
                $file->move(ROOTPATH . 'public_html/uploads' . $options['upload_path'], $file->getRandomName());
            }
            $fullPath = 'uploads' . $options['upload_path'] . $file->getFilename();
        }
        if ($options['private'] ?? false) {
            $fullPath = esc($fullPath, 'url');
        }
        return $fullPath;
    }

    /**
     * Convert to webp and compress
     *
     * @param	object	$file
     * @return	File
     */
    private function convertToWebp($filePath, int $maxSize = 0)
    {
        $file = new File($filePath, true);
        if (!$file) {
            return FALSE;
        }
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
        unlink($filePath);
        return $newFile;
    }
}
