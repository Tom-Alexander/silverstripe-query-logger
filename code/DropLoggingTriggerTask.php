<?php

class DropLoggingTriggerTask extends BuildTask
{

    public function run($request)
    {
        DB::query("DROP TRIGGER IF EXISTS RecordLoggingTrigger");
        echo "Dropped RecordLoggingTrigger";
    }

}