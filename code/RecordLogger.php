<?php

class RecordLogger extends DataObject
{
    private static $db = array(
        'TableRecordField'  => 'Text',
        'DuringOnBeforeWrite' => 'Boolean',
        'DuringOnBeforeQuery' => 'Boolean',
        'DuringMemberTrigger' => 'Boolean'
    );

    public static function getAllowedActions()
    {
        return array('SELECT', 'UPDATE', 'INSERT', 'DELETE');
    }

    public static function getDebugOperation()
    {
        $operation = Config::inst()->get('RecordLogger', 'debugOperation');
        if(is_string($operation) && in_array($operation, RecordLogger::getAllowedActions())) {
            return $operation;
        } else {
           // throw new Exception('Invalid debug operation');
        }
    }

    public static function getDebugTableName()
    {
        return Config::inst()->get('RecordLogger', 'debugTableName');
    }

    public static function saveDebugInfo($tableRecordField, $beforeWrite = true, $beforeQuery = false, $beforeTrigger = false)
    {
        $recordDebug = new RecordLogger();
        $recordDebug->TableRecordField = $tableRecordField;
        $recordDebug->DuringOnBeforeWrite = $beforeWrite;
        $recordDebug->DuringOnBeforeQuery = $beforeQuery;
        $recordDebug->DuringMemberTrigger = $beforeTrigger;
        $recordDebug->write();
    }

}