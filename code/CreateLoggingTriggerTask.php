<?php

class CreateLoggingTriggerTask extends BuildTask
{

    public function run($request)
    {

        $operation = RecordLogger::getDebugOperation();
        $table = RecordLogger::getDebugTableName();

        DB::query("DELIMITER //
             CREATE TRIGGER RecordLoggingTrigger
             AFTER DELETE ON `Member` FOR EACH ROW
             BEGIN

                INSERT INTO RecordLogger (
                    TableRecordField,
                    Created,
                    LastEdited,
                    DuringOnBeforeWrite,
                    DuringOnBeforeQuery,
                    DuringMemberTrigger
                )

                VALUES (
                    OLD.ID,
                    SYSDATE(),
                    SYSDATE(),
                    FALSE,
                    FALSE,
                    TRUE
                );

             END; //
             DELIMITER ;");
        echo "Created RecordLoggingTrigger";
    }

}