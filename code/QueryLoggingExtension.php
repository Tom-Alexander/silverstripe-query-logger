<?php

class QueryLoggingExtension extends DataExtension
{

    public function onBeforeDelete()
    {
        parent::onBeforeDelete();
        RecordLogger::saveDebugInfo($this->owner->ID);
    }

}