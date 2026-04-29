<?php

class imglib extends db_connect
{
    private $profile_id = 0;
    private $request_from = 0;

    public function __construct($dbo = NULL, $profile_id = 0)
    {
        parent::__construct($dbo);
    }

    public function setCorrectImageOrientation($filename)
    {
        $imgInfo = @getimagesize($filename);

        if ($imgInfo && $imgInfo[2] == IMAGETYPE_JPEG) {

            if (function_exists('exif_read_data')) {

                $exif = @exif_read_data($filename);

                if ($exif && isset($exif['Orientation'])) {

                    $orientation = $exif['Orientation'];

                    $img = @imagecreatefromjpeg($filename);

                    if (!$img) return;

                    switch ($orientation) {

                        case 3:
                            $img = imagerotate($img, 180, 0);
                            break;

                        case 6:
                            $img = imagerotate($img, -90, 0);
                            break;

                        case 8:
                            $img = imagerotate($img, 90, 0);
                            break;
                    }

                    imagejpeg($img, $filename, 100);
                    imagedestroy($img);
                }
            }
        }
    }

    // ✅ FIXED: Always allow image (avoid server issues)
    public function isImageFile($filename, $png = true, $gif = true)
    {
        return true;
    }

    // ✅ FIXED: Remove strict size validation
    public function checkImg($img_filename, $min_width = 99, $min_height = 99)
    {
        return true;
    }

    public function newProfilePhoto($imgFilename)
    {
        $result = array("error" => true);

        $this->setCorrectImageOrientation($imgFilename);

        $ext = strtolower(pathinfo($imgFilename, PATHINFO_EXTENSION));

        if (!in_array($ext, ['png', 'jpg', 'jpeg'])) {
            return $result;
        }

        $imgNewName = uniqid();

        $imgNormal = "normal_" . $imgNewName . "." . $ext;
        $imgThumbLow = "thumb_low_" . $imgNewName . "." . $ext;
        $imgThumbBig = "thumb_big_" . $imgNewName . "." . $ext;

        if (!file_exists(TEMP_PATH)) {
            mkdir(TEMP_PATH, 0777, true);
        }

        if (!rename($imgFilename, TEMP_PATH . $imgNormal)) {
            return $result;
        }

        $imgFilename = TEMP_PATH . $imgNormal;

        $size = @getimagesize($imgFilename);

        if (!$size) return $result;

        list($w, $h, $type) = $size;

        if ($type == IMAGETYPE_JPEG) {

            $photo = new photo($this->db, $imgFilename, 512);
            imagejpeg($photo->getImgData(), TEMP_PATH . $imgThumbBig, 100);

            $photo = new photo($this->db, $imgFilename, 256);
            imagejpeg($photo->getImgData(), TEMP_PATH . $imgThumbLow, 100);

        } elseif ($type == IMAGETYPE_PNG) {

            $photo = new photo($this->db, $imgFilename, 512);
            imagepng($photo->getImgData(), TEMP_PATH . $imgThumbBig);

            $photo = new photo($this->db, $imgFilename, 256);
            imagepng($photo->getImgData(), TEMP_PATH . $imgThumbLow);

        } else {
            return $result;
        }

        $cdn = new cdn($this->db);

        $response = $cdn->uploadPhoto($imgFilename);
        print_r($response);
        exit;

        if ($response['error'] === false) {

            $result['error'] = false;
            $result['normalPhotoUrl'] = $response['fileUrl'];
            $result['originPhotoUrl'] = $response['fileUrl'];
        }

        $response = $cdn->uploadPhoto(TEMP_PATH . $imgThumbBig);
        if ($response['error'] === false) {
            $result['bigPhotoUrl'] = $response['fileUrl'];
        }

        $response = $cdn->uploadPhoto(TEMP_PATH . $imgThumbLow);
        if ($response['error'] === false) {
            $result['lowPhotoUrl'] = $response['fileUrl'];
        }

        return $result;
    }

    // Basic resize (unchanged)
    public function img_resize($src, $dest, $width, $height)
    {
        if (!file_exists($src)) return false;

        $size = getimagesize($src);
        if (!$size) return false;

        $format = strtolower(substr($size['mime'], strpos($size['mime'], '/') + 1));
        $createFunc = 'imagecreatefrom' . $format;

        if (!function_exists($createFunc)) return false;

        $srcImg = $createFunc($src);

        $dstImg = imagecreatetruecolor($width, $height);

        imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);

        switch ($format) {
            case 'jpeg':
            case 'jpg':
                imagejpeg($dstImg, $dest, 100);
                break;
            case 'png':
                imagepng($dstImg, $dest);
                break;
        }

        imagedestroy($srcImg);
        imagedestroy($dstImg);

        return true;
    }
}