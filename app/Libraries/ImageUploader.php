<?php

namespace App\Libraries;

use CodeIgniter\Files\File;

/**
 * ImageUploader that use Codeigniter 4's File Uploader Class
 * 
 * This class has procedure to compress images and convert them into webp.
 * 
 * @codeCoverageIgnore
 * 
 * @author Azvya Erstevan I
 * @package KartasolvApp\Libraries
 */
class ImageUploader
{
    /**
     * Upload method.
     * @param array $options Options taken from the Controllers.
     * @param \CodeIgniter\Files\File|null $file File that uploaded by the input or (multiple input).
     * @return mixed Boolean result or Uploaded Filepath.
     */
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
            $imgs = $request->getFileMultiple($options['name']);
            $opt = [
                'private' => $options['private'] ?? false,
                'upload_path' => $options['upload_path'],
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
            $filepath = $this->convertToWebp($unConverted);
            if (!$filepath) {
                return FALSE;
            }
            $file = new File($filepath);
            if ((!($options['private'] ?? false))) {
                if (!is_dir(ROOTPATH . 'public_html/uploads' . $options['upload_path'])) {
                    mkdir(ROOTPATH . 'public_html/uploads' . $options['upload_path'], 0777, true);
                };
                $newFilename = $file->move(ROOTPATH . 'public_html/uploads' . $options['upload_path'], $file->getRandomName())->getFilename();
            }
            $fullPath = 'uploads' . $options['upload_path'] . ($newFilename ?? $file->getFilename());
        }
        if ($options['private'] ?? false) {
            $fullPath = esc($fullPath, 'url');
        }
        return $fullPath;
    }

    /**
     * Convert file in addressed filepath into webp.
     * 
     * Convert to webp and compress
     *
     * @param	object	$file Filepath.
     * @return	File Converted File.
     */
    private function convertToWebp($filePath)
    {
        $file = new File($filePath, true);
        if (!$file) {
            return FALSE;
        }
        $file->getFilename();
        $mimeType = $file->getMimeType();
        if ($mimeType == 'image/webp') {
            return TRUE;
        }
        $quality = 80;

        $info = getimagesize($file);
        $img = \Config\Services::image('');
        $img->withFile($file);
        if ($info[0] > 2048 or $info[1] > 2048) {
            $img->resize(2048, 2048, true);
        }
        $newFile = str_replace('.' . $file->getExtension(), '.webp', str_replace($file->getFilename(), $file->getRandomName(), $filePath));
        $img->convert(IMAGETYPE_WEBP);
        $img->save($newFile, $quality);
        unlink($filePath);
        return $newFile;
    }
}
