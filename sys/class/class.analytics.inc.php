<?php

/*!
 * https://raccoonsquare.com, https://raccoonjohn.com
 * raccoonsquare@gmail.com
 *
 * Copyright 2012-2026 Demianchuk Dmytro and Raccoon John (raccoonsquare@gmail.com)
 */

class analytics extends db_connect
{

    private $timezone = "Europe/Kyiv";
    private $requestFrom = 0;

    public function __construct($dbo = NULL)
    {
        parent::__construct($dbo);
    }

    /**
     * @throws DateMalformedStringException
     * @throws DateInvalidTimeZoneException
     */
    public function getTimeStamps($days): array
    {
        $timezone = new DateTimeZone($this->timezone);
        $dates = [];

        for ($i = 0; $i < $days; $i++) {

            $date = new DateTime('today', $timezone);
            $date->modify("-$i days");
            $dates[] = $date->getTimestamp();
        }

        return array_reverse($dates);
    }

    /**
     * @throws DateMalformedStringException
     * @throws DateInvalidTimeZoneException
     */
    public function getItemsCount($days, $tableName, $timeField): array
    {
        $result = [];

        $timestamps = $this->getTimeStamps($days);

        foreach ($timestamps as $timestamp) {

            $endTime = $timestamp + 86400; // 24 hours

            $stmt = $this->db->prepare("SELECT count(*) FROM {$tableName} WHERE {$timeField} > {$timestamp} AND {$timeField} < {$endTime}");
            $stmt->execute();

            $result[] = $stmt->fetchColumn();
        }

        return $result;
    }

    public function getTimedItemsCountByIntField($days, $tableName, $timeField, $fieldName, $filedValue): array
    {
        $result = [];

        $timestamps = $this->getTimeStamps($days);

        foreach ($timestamps as $timestamp) {

            $endTime = $timestamp + 86400; // 24 hours

            $stmt = $this->db->prepare("SELECT count(*) FROM {$tableName} WHERE {$timeField} > {$timestamp} AND {$timeField} < {$endTime} AND {$fieldName} = {$filedValue}");
            $stmt->execute();

            $result[] = $stmt->fetchColumn();
        }

        return $result;
    }

    public function getItemsCountByNotEmptyTextField($tableName, $fieldName): int
    {
        $stmt = $this->db->prepare("SELECT count(*) FROM {$tableName} WHERE {$fieldName} <> ''");
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    public function getItemsCountByTextField($tableName, $fieldName, $fieldValue): int
    {
        $stmt = $this->db->prepare("SELECT count(*) FROM {$tableName} WHERE {$fieldName} = {$fieldValue}");
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    public function getItemsCountByIntField($tableName, $fieldName, $fieldValue): int
    {
        $stmt = $this->db->prepare("SELECT count(*) FROM {$tableName} WHERE {$fieldName} = {$fieldValue}");
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    public function setRequestFrom($requestFrom): void
    {
        $this->requestFrom = $requestFrom;
    }

    public function getRequestFrom(): int
    {
        return $this->requestFrom;
    }
}

