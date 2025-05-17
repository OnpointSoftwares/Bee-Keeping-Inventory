<?php
require_once(__DIR__ . '/../../model/health/HealthModel.php');

class HealthController {
    public $healthModel;

    public function __construct($db) {
        $this->healthModel = new HealthModel($db);
    }

    public function addHealthRecord($input) {
        return $this->healthModel->addHealthRecord($input);
    }

    public function getHealthRecords($hiveID) {
        return $this->healthModel->getHealthRecords($hiveID);
    }
}