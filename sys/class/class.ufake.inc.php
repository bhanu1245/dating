<?php

/*!
 * https://raccoonsquare.com
 * raccoonsquare@gmail.com
 *
 * Copyright 2012-2025 Demyanchuk Dmitry (raccoonsquare@gmail.com)
 */

class ufake extends db_connect
{
    private $requestFrom = 0;

    public function __construct($dbo = NULL)
    {
        parent::__construct($dbo);

        try {

            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $this->db->prepare(
                "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = :tableName 
            AND COLUMN_NAME = :columnName"
            );

            $stmt->execute([
                ':tableName' => "users",
                ':columnName' => "ufake"
            ]);

            if (!$stmt->fetchColumn()) {

                $stmt = $this->db->prepare("ALTER TABLE users ADD ufake INT(10) UNSIGNED DEFAULT 0 after state");
                $stmt->execute();
            }

        } catch (PDOException $e) {

            // Handle the exception, e.g., log the error
            //error_log("Error checking column existence: " . $e->getMessage());
        }
    }

    function createAccounts($locations, $names, $surnames, $photos, $photos_dir, $ageFrom, $ageTo, $sex, $count) {

        for ($i = 0; $i < $count; $i++) {

            $locationRandomKey = array_rand($locations);
            $nameRandomKey = array_rand($names);
            $surnameRandomKey = array_rand($surnames);

            //

            $photoRandomKey = array_rand($photos);
            $photoUrl = APP_URL."/ufake/".$photos_dir."/".$photos[$photoRandomKey];

            //

            $age = random_int($ageFrom, $ageTo);

            // Login

            $login = "uf".helper::generateId(7)."f";

            // Password

            $password = $login."111";

            // Email

            $email = $login."1112@gmail.com";

            //

            $account = new account($this->db);
            //$res = $account->signup($login, $names[$nameRandomKey]." ".$surnames[$surnameRandomKey], $password, $email);
            $res = $account->signup($login, $names[$nameRandomKey]." ".$surnames[$surnameRandomKey], $password, $email, $sex, 2007, 1, 10, $age, 1, 'en', $i + 1);

            if (!$res['error']) {

                $account->setId($res['accountId']);

                // Make account as Fake account

                $this->setFake($res['accountId'], 1);

                //

                //$account->setSex($sex);
                //$account->setAge($age);

                // Location

                $locData = explode(",", $locations[$locationRandomKey]);

                $account->setLocation($locData[0].','.$locData[1]);
                $account->setGeoLocation($locData[2], $locData[3]);

                $account->setAllowMessages(1);

                // Photo

                $photo_data = array(
                    "originPhotoUrl" => $photoUrl,
                    "normalPhotoUrl" => $photoUrl,
                    "bigPhotoUrl" => $photoUrl,
                    "lowPhotoUrl" => $photoUrl
                );

                $account->setPhoto($photo_data);
            }

            unset($account);

            //echo $names[$nameRandomKey]." ".$surnames[$surnameRandomKey]." ".$age." ".$photoUrl." ".$locations[$locationRandomKey]." ".$sex." ".$login."<br>";
        }

        $result = [];

        return $result;
    }

    public function deactivateAll()
    {
        $result = array(
            "error" => true,
            "error_code" => ERROR_UNKNOWN
        );

        $stmt = $this->db->prepare("SELECT id FROM users WHERE state = 0 AND ufake <> 0");

        if ($stmt->execute()) {

            while ($row = $stmt->fetch()) {

                $account = new account($this->db);
                $account->setId($row['id']);
                $account->deactivation();
                unset($account);
            }
        }

        return $result;
    }

    public function setFake($account_id, $status)
    {
        $result = array(
            "error" => true,
            "error_code" => ERROR_UNKNOWN
        );

        $registrationComplete = 1;

        $stmt = $this->db->prepare("UPDATE users SET ufake = (:ufake), registrationComplete = (:registrationComplete) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $account_id, PDO::PARAM_INT);
        $stmt->bindParam(":ufake", $status, PDO::PARAM_INT);
        $stmt->bindParam(":registrationComplete", $registrationComplete, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $result = array(
                'error' => false,
                'error_code' => ERROR_SUCCESS
            );
        }

        return $result;
    }

    public function getAccountsCountByState($state)
    {
        $stmt = $this->db->prepare("SELECT count(*) FROM users WHERE ufake <> 0 AND state = (:state)");
        $stmt->bindParam(":state", $state, PDO::PARAM_INT);
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    public function getAccountsCountByGender($gender)
    {
        $stmt = $this->db->prepare("SELECT count(*) FROM users WHERE sex = (:gender) AND state = 0 AND ufake <> 0");
        $stmt->bindParam(":gender", $gender, PDO::PARAM_INT);
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    public function getFileNames($directory) {

        $result = [];

        if (is_dir($directory)) {

            // Open directory handle
            $handle = opendir($directory);

            if ($handle) {

                while (($file = readdir($handle)) !== false) {

                    $filePath = $directory . DIRECTORY_SEPARATOR . $file;

                    if (is_file($filePath) && preg_match('/\.jpe?g$/i', $file)) {

                        $result[] = $file;
                    }
                }

                closedir($handle);
            }

        } else {

            echo "Directory not found: $directory";
        }

        return $result;
    }

    public function getFileLines($filename) {

        $result = [];

        if (file_exists($filename)) {

            // Read file lines into an array, trimming newlines
            $result = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        }

        return $result;
    }

    public function setRequestFrom($requestFrom)
    {
        $this->requestFrom = $requestFrom;
    }

    public function getRequestFrom()
    {
        return $this->requestFrom;
    }
}

