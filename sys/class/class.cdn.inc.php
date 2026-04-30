<?php

class cdn extends db_connect
{
    public function __construct($dbo = NULL)
    {
        parent::__construct($dbo);
    }

    private function saveLocal($filePath, $folder)
    {
        $result = array(
            "error" => true,
            "msg" => "failed",
            "fileUrl" => ""
        );

        $folder = trim((string) $folder, '/');
        if ($folder === '') {
            return $result;
        }

        $docRoot = rtrim((string) ($_SERVER['DOCUMENT_ROOT'] ?? ''), '/');
        if ($docRoot === '') {
            $docRoot = rtrim(dirname(__DIR__, 3), '/');
        }

        $baseDir = $docRoot.'/'.$folder.'/';

        if (!is_dir($baseDir) && !@mkdir($baseDir, 0775, true)) {
            return $result;
        }

        if (!is_writable($baseDir)) {
            return $result;
        }

        $fileName = basename($filePath);
        $target = $baseDir.$fileName;

        if (@copy($filePath, $target)) {
            $result['error'] = false;
            $result['msg'] = 'success';
            $result['fileUrl'] = APP_URL.'/'.trim($folder, '/').'/'.$fileName;
        }

        @unlink($filePath);

        return $result;
    }

    public function uploadPhoto($imgFilename) { return $this->saveLocal($imgFilename, PHOTO_PATH); }
    public function uploadCover($imgFilename) { return $this->saveLocal($imgFilename, COVER_PATH); }
    public function uploadMyPhoto($imgFilename) { return $this->saveLocal($imgFilename, MY_PHOTOS_PATH); }
    public function uploadChatImg($imgFilename) { return $this->saveLocal($imgFilename, CHAT_IMAGE_PATH); }
    public function uploadVideo($imgFilename) { return $this->saveLocal($imgFilename, VIDEO_PATH); }
}
