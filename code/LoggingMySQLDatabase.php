<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use PHPSQL\Parser;

class LoggingMySQLDatabase extends MySQLDatabase
{

    private $logger;

    public function __construct($parameters)
    {
        parent::__construct($parameters);
        $this->logger = new Logger('RecordDebug');
        $handler = new StreamHandler(BASE_PATH . '/../logs/RecordDebug.log', Logger::INFO);
        $handler->setFormatter(new LineFormatter(null, null, false, true));
        $this->logger->pushHandler($handler);
    }

    public function query($sql, $errorLevel = E_USER_ERROR)
    {
        $this->onBeforeQuery($sql, $errorLevel);
        return parent::query($sql, $errorLevel);
    }

    private function getTableFromSyntaxTree($parsed)
    {
        if(isset($parsed['FROM']) && is_array($parsed['FROM'])) {
            foreach($parsed['FROM'] as $expression) {
                if($expression['expr_type'] == 'table') {
                    return str_replace('"', '', $expression['table']);
                }
            }
        }
    }

    private function getOperationFromSyntaxTree($parsed)
    {
        $allowed = RecordLogger::getAllowedActions();
        foreach($parsed as $key => $expression) {
            $operation = str_replace('"', '', $key);
            if(in_array($operation, $allowed)) {
                return $operation;
            }
        }
    }

    private function getFieldValueFromSyntaxTree($parsed)
    {
        foreach($parsed as $key => $statement) {
            foreach($statement as $expression) {
                if(isset($expression['expr_type']) && isset($expression['base_expr'])) {
                    if(strpos($expression['base_expr'], 'ID') !== false) {
                        preg_match_all('/\d+/', $expression['base_expr'], $matches);
                        if(isset($matches[0]) && count($matches[0]) > 0) {
                            return str_replace('"', '', $matches[0][0]);
                        }
                    }
                }
            }
        }
    }

    protected function onBeforeQuery($sql)
    {

        $table = RecordLogger::getDebugTableName();
        $operation = RecordLogger::getDebugOperation();

        if(strstr($sql, $operation) !== false && strstr($sql, $table) !== false) {
            $parser = new Parser();
            $parsed = $parser->parse($sql);
            if (isset($parsed) && is_array($parsed)) {
                if($this->getTableFromSyntaxTree($parsed) == $table) {
                    if($this->getOperationFromSyntaxTree($parsed) == $operation) {
                        $value = $this->getFieldValueFromSyntaxTree($parsed);
                        RecordLogger::saveDebugInfo($value, false, true, false);
                        $this->logger->warn($sql);
                        $this->logger->warn(debug_backtrace());
                    }
                }
            }

        }

    }

}