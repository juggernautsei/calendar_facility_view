<?php

/*
 * package   OpenEMR
 * link           https://open-emr.org
 * author      Sherwin Gaddis <sherwingaddis@gmail.com>
 * Copyright (c) 2024.  Sherwin Gaddis <sherwingaddis@gmail.com>
 */

namespace Juggernaut\Quest\Module\Services;

use OpenEMR\Common\Database\QueryUtils;
use OpenEMR\Common\Logging\SystemLogger;
class ImportCompendiumData
{
    private string|false $compendiumData;
    private $logging;
    private $name;
    private $description;
    private $code;
    private string $tableName;
    private $pcode;
    private $qcode;
    private $seq;
    private $options;
    private $tips;
    private $qtext;
    private $compendiumAoeData;

    public function __construct()
    {
        $this->logging = new SystemLogger();
        $insert = new QueryUtils();
        $this->tableName = $insert::escapeTableName('procedure_type');
        //unzipped data files
        $compendium = dirname(__DIR__, 6) . '/sites/' . $_SESSION['site_id'] . '/documents/temp/ORDCODE_TMP.TXT';
        $aoe = dirname(__DIR__, 6) . '/sites/' . $_SESSION['site_id'] . '/documents/temp/AOE_TMP.TXT';

        if(file_exists($compendium)) {
            $this->compendiumData = file_get_contents($compendium);
            $this->importData();
            unlink($compendium);
        } else {
            $this->logging->error('File not found: Check sites/documents/temp directory permission');
        }
        if(file_exists($aoe)) {
            $this->compendiumAoeData = file_get_contents($aoe);
            $this->importAoeData();
            unlink($aoe);
        } else {
            $this->logging->error('File not found: Check sites/documents/temp directory permission');
        }
    }

    private function importAoeData(): void
    {
        if ($this->checkPrimarykey() == 0) {
            //if the primary key is not set then drop it because there will be duplicates
            sqlStatement("ALTER TABLE procedure_questions DROP PRIMARY KEY");
        } else {
            //if there are records we need to clear out the questions and then reload them
            $this->reloadQuestionBank();
        }

        $i = 0; //first array can't be used, need to skip it

        $lines = explode("\n", $this->compendiumAoeData);
        //loop through the data and insert it into the database skipping first array
        foreach ($lines as $line) {
            if ($i == 0) {
                $i++;
                continue;
            }
            $fields = explode("^", $line);
            $this->pcode = $fields[3] ?? '';
            $this->qcode = $fields[2] ?? '';
            $this->seq = $fields[4] ?? '';
            $this->tips = $fields[11] ?? '';
            $this->options = $fields[9] ?? '';
            $this->qtext = $fields[5] ?? '';
            $this->insertAoeData();

            $i++;
        }
    }
    private function importData(): void
    {
        $this->createQuestDatasetGroup();
        $i = 0; //first array can't be used, need to skip it
        $lines = explode("\n", $this->compendiumData);
        //loop through the data and insert it into the database skipping first array
        foreach ($lines as $line) {
            if ($i == 0) {
                $i++;
                continue;
            }
            $fields = explode("^", $line);
            $this->name = $fields[6] ?? '';
            $this->description = $fields[6] ?? '';
            $this->code = $fields[1] ?? '';

            if ($this->checkIfDataExists()) {
                $i++;
                continue;
            }
            $this->insertData();
            $i++;
        }
    }
    private function insertData(): void
    {
        $providerId = $this->getQuestProviderId();  //this is a function that returns the provider id from order providers
        $parent = $this->dataSetGroup(); //this is a function that returns the group number from procedure_type

        if (!isset($providerId['ppid']) || !isset($parent['procedure_type_id'])) {
            $msg = xlt('Quest provider or type not found ');
            $this->logging->error($msg);
            return;
        }
        if (!isset($this->name) || !isset($this->code) || !isset($this->description)) {
            $msg = xlt('Quest name, code or description not found ');
            $this->logging->error($msg);
            return;
        }
        $sql = "INSERT INTO `procedure_type` (`procedure_type_id`, `parent`, `name`, `lab_id`,
`procedure_code`, `procedure_type`, `body_site`, `specimen`, `route_admin`, `laterality`,
 `description`, `standard_code`, `related_code`, `units`, `range`, `seq`, `activity`, `notes`, `transport`, `procedure_type_name`) VALUES
(NULL, ?, ?, 1, ?, 'ord', '', '', '', '', ?, '', '', '', '', 0, 1, '', NULL, 'laboratory_test')";
        sqlStatement($sql, [$parent['procedure_type_id'], $this->name, $this->code, $this->description]);
    }

    private function insertAoeData(): void
    {
        //TODO: There need to be a better way to handle inserting and updating data to avoid duplicate records. or is there a need to duplicate
        $this->tableName = 'procedure_questions';
        $labId = $this->getQuestProviderId();
        $labId = $labId['ppid'];
        if (!isset($labId)) {
            $msg = xlt('Quest provider not found ');
            $this->logging->error($msg);
            return;
        }
        //if the data is new then insert it and ignore primary key

        $sql = "INSERT INTO `procedure_questions` (`lab_id`, `procedure_code`, `question_code`, `seq`, `question_text`,
                               `required`, `maxsize`, `fldtype`, `options`, `tips`, `activity`)
        VALUES (?, ?, ?, ?, ?, '1', '0', 'T', ?, ?, '1')";
        sqlStatement(
            $sql,
            array($labId, $this->pcode, $this->qcode, $this->seq, $this->qtext, $this->options, $this->tips)
        );
    }

    private function createQuestDatasetGroup(): void
    {
        //does the dataset group exist because this will be done multiple times.
        $dataGroup = $this->dataSetGroup();
        if (!empty($dataGroup['procedure_type_id'])) {
            //if it does exist then do nothing
            return;
        }
        //if not create the group
        $createGroup = "INSERT INTO `procedure_type`
    (`procedure_type_id`,
     `parent`,
     `name`,
     `lab_id`,
     `procedure_code`,
     `procedure_type`,
     `body_site`,
     `specimen`,
     `route_admin`,
     `laterality`,
     `description`,
     `standard_code`,
     `related_code`,
     `units`,
     `range`,
     `seq`,
     `activity`,
     `notes`,
     `transport`,
     `procedure_type_name`
     ) VALUES
    (NULL, 0, 'Quest Clinical Dataset', 1, '', 'grp', '', '', '', '', 'Quest Clinical Dataset', '', '', '', '', 0, 1, '', NULL, 'procedure')";
        sqlStatement($createGroup);
    }

    public function dataSetGroup(): array
    {
        return sqlQuery("SELECT `procedure_type_id` FROM `procedure_type` WHERE `name` = 'Quest Clinical Dataset'");
    }

    public function getQuestProviderId(): array
    {
        $this->tableName = 'procedure_providers';
        return sqlQuery("SELECT `ppid` FROM $this->tableName WHERE `name` = ?", ['Quest']);
    }

    private function checkIfDataExists(): bool
    {
        $sql = "SELECT `procedure_type_id` FROM `procedure_type` WHERE `procedure_code` = ?";
        $data = sqlQuery($sql, [$this->code]);
        return !empty($data['procedure_type_id']);
    }

    private function reloadQuestionBank(): bool
    {
        sqlStatement("DROP TABLE IF EXISTS `procedure_questions`");
        $sql = "CREATE TABLE `procedure_questions` (
  `lab_id`              bigint(20)   NOT NULL DEFAULT 0   COMMENT 'references procedure_providers.ppid to identify the lab',
  `procedure_code`      varchar(31)  NOT NULL DEFAULT ''  COMMENT 'references procedure_type.procedure_code to identify this order type',
  `question_code`       varchar(31)  NOT NULL DEFAULT ''  COMMENT 'code identifying this question',
  `seq`                 int(11)      NOT NULL default 0   COMMENT 'sequence number for ordering',
  `question_text`       varchar(255) NOT NULL DEFAULT ''  COMMENT 'descriptive text for question_code',
  `required`            tinyint(1)   NOT NULL DEFAULT 0   COMMENT '1 = required, 0 = not',
  `maxsize`             int          NOT NULL DEFAULT 0   COMMENT 'maximum length if text input field',
  `fldtype`             char(1)      NOT NULL DEFAULT 'T' COMMENT 'Text, Number, Select, Multiselect, Date, Gestational-age',
  `options`             text                              COMMENT 'choices for fldtype S and T',
  `tips`                varchar(255) NOT NULL DEFAULT ''  COMMENT 'Additional instructions for answering the question',
  `activity`            tinyint(1)   NOT NULL DEFAULT 1   COMMENT '1 = active, 0 = inactive'
) ENGINE=InnoDB";
        sqlStatement($sql);
        return true;
    }

    private function checkPrimarykey(): bool
    {
        $sql = "SELECT COUNT(*) AS row_count FROM procedure_questions";
        $data = sqlQuery($sql);

        return $data['row_count'];
    }
}
