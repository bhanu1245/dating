<?php

class cdn extends db_connect
{
    public function __construct($dbo = NULL)
    {
        parent::__construct($dbo);
    }

    // 🔥 CORE LOCAL UPLOAD FUNCTION
    private function saveLocal($filePath, $folder)
    {
        $result = array(
            "error" => true,
            "error_code" => ERROR_UNKNOWN,
            "fileUrl" => ""
        );

        $baseDir = $_SERVER['DOCUMENT_ROOT'] . "/" . $folder;

        // Create folder if not exists
        if (!file_exists($baseDir)) {
            mkdir($baseDir, 0777, true);
        }

        $fileName = basename($filePath);
        $target = $baseDir . $fileName;

        if (copy($filePath, $target)) {

            $result['error'] = false;
            $result['error_code'] = ERROR_SUCCESS;
            $result['fileUrl'] = APP_URL . "/" . $folder . $fileName;

        }

        @unlink($filePath);

        return $result;
    }

    // PROFILE PHOTO
    public function uploadPhoto($imgFilename)
    {
        return $this->saveLocal($imgFilename, "uploads/photos/");
    }

    // COVER PHOTO
    public function uploadCover($imgFilename)
    {
        return $this->saveLocal($imgFilename, "uploads/covers/");
    }

    // GALLERY PHOTO
    public function uploadMyPhoto($imgFilename)
    {
        return $this->saveLocal($imgFilename, "uploads/gallery/");
    }

    // CHAT IMAGE
    public function uploadChatImg($imgFilename)
    {
        return $this->saveLocal($imgFilename, "uploads/chat/");
    }

    // VIDEO
    public function uploadVideo($imgFilename)
    {
        return $this->saveLocal($imgFilename, "uploads/videos/");
    }
}