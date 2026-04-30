<?php

class imglib extends db_connect
{
    public function __construct($dbo = NULL, $profile_id = 0)
    {
        parent::__construct($dbo);
    }

    public function setCorrectImageOrientation($filename)
    {
        $imgInfo = @getimagesize($filename);
        if ($imgInfo && $imgInfo[2] == IMAGETYPE_JPEG && function_exists('exif_read_data')) {
            $exif = @exif_read_data($filename);
            if ($exif && isset($exif['Orientation'])) {
                $img = @imagecreatefromjpeg($filename);
                if (!$img) return;
                switch ((int) $exif['Orientation']) {
                    case 3: $img = imagerotate($img, 180, 0); break;
                    case 6: $img = imagerotate($img, -90, 0); break;
                    case 8: $img = imagerotate($img, 90, 0); break;
                }
                imagejpeg($img, $filename, 100);
                imagedestroy($img);
            }
        }
    }

    public function isImageFile($filename, $png = true, $gif = true)
    {
        $imageInfo = @getimagesize($filename);
        if (!$imageInfo) return false;

        $allowed = array(IMAGETYPE_JPEG);
        if ($png) $allowed[] = IMAGETYPE_PNG;
        if ($gif) $allowed[] = IMAGETYPE_GIF;

        return in_array($imageInfo[2], $allowed, true);
    }

    public function checkImg($img_filename, $min_width = 99, $min_height = 99)
    {
        $size = @getimagesize($img_filename);
        return $size && $size[0] >= $min_width && $size[1] >= $min_height;
    }

    private function ensureTempDir()
    {
        return is_dir(TEMP_PATH) || @mkdir(TEMP_PATH, 0775, true);
    }

    public function newProfilePhoto($imgFilename)
    {
        $result = array("error" => true);
        if (!$this->ensureTempDir()) return $result;

        $this->setCorrectImageOrientation($imgFilename);
        $ext = strtolower(pathinfo($imgFilename, PATHINFO_EXTENSION));
        if (!in_array($ext, array('png', 'jpg', 'jpeg'))) $ext = 'jpg';

        $imgNewName = uniqid();
        $imgNormal = "normal_{$imgNewName}.{$ext}";
        $imgThumbLow = "thumb_low_{$imgNewName}.{$ext}";
        $imgThumbBig = "thumb_big_{$imgNewName}.{$ext}";

        if (!@copy($imgFilename, TEMP_PATH.$imgNormal)) return $result;
        $imgFilename = TEMP_PATH.$imgNormal;

        $photo = new photo($this->db, $imgFilename, 512);
        if ($ext === 'png') imagepng($photo->getImgData(), TEMP_PATH.$imgThumbBig); else imagejpeg($photo->getImgData(), TEMP_PATH.$imgThumbBig, 100);

        $photo = new photo($this->db, $imgFilename, 256);
        if ($ext === 'png') imagepng($photo->getImgData(), TEMP_PATH.$imgThumbLow); else imagejpeg($photo->getImgData(), TEMP_PATH.$imgThumbLow, 100);

        $cdn = new cdn($this->db);
        $response = $cdn->uploadPhoto($imgFilename);
        if ($response['error']) return $result;

        $result['error'] = false;
        $result['normalPhotoUrl'] = $response['fileUrl'];
        $result['originPhotoUrl'] = $response['fileUrl'];

        $response = $cdn->uploadPhoto(TEMP_PATH.$imgThumbBig);
        $result['bigPhotoUrl'] = $response['error'] ? $result['normalPhotoUrl'] : $response['fileUrl'];

        $response = $cdn->uploadPhoto(TEMP_PATH.$imgThumbLow);
        $result['lowPhotoUrl'] = $response['error'] ? $result['normalPhotoUrl'] : $response['fileUrl'];

        return $result;
    }

    public function createMyPhoto($tmpName, $originalName)
    {
        $result = array("error" => true);
        if (!$this->ensureTempDir()) return $result;

        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if (!in_array($ext, array('jpg', 'jpeg', 'png'))) $ext = 'jpg';

        $tmpFile = TEMP_PATH.uniqid('gallery_').'.'.$ext;
        if (!@copy($tmpName, $tmpFile)) return $result;

        $cdn = new cdn($this->db);
        $upload = $cdn->uploadMyPhoto($tmpFile);
        if ($upload['error']) return $result;

        return array(
            "error" => false,
            "previewUrl" => $upload['fileUrl'],
            "normalUrl" => $upload['fileUrl'],
            "originUrl" => $upload['fileUrl'],
            "previewPhotoUrl" => $upload['fileUrl'],
            "normalPhotoUrl" => $upload['fileUrl'],
            "originPhotoUrl" => $upload['fileUrl'],
            "imgUrl" => $upload['fileUrl'],
            "originImgUrl" => $upload['fileUrl']
        );
    }

    public function createChatImg($tmpName, $originalName)
    {
        $result = array("error" => true);
        if (!$this->ensureTempDir()) return $result;

        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if (!in_array($ext, array('jpg', 'jpeg', 'png'))) $ext = 'jpg';

        $tmpFile = TEMP_PATH.uniqid('chat_').'.'.$ext;
        if (!@copy($tmpName, $tmpFile)) return $result;

        $cdn = new cdn($this->db);
        $upload = $cdn->uploadChatImg($tmpFile);
        if ($upload['error']) return $result;

        return array("error" => false, "imgUrl" => $upload['fileUrl']);
    }
}
