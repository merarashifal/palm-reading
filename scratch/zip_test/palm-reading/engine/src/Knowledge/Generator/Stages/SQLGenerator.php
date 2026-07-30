<?php

namespace AIAnalysisEngine\Knowledge\Generator\Stages;

use AIAnalysisEngine\Knowledge\Generator\BuildContext;
use AIAnalysisEngine\Pipeline\PipelineContext;
use AIAnalysisEngine\Pipeline\PipelineResult;
use AIAnalysisEngine\Pipeline\PipelineStageInterface;

class SQLGenerator implements PipelineStageInterface
{
    public function name(): string
    {
        return 'SQLGenerator';
    }

    public function execute(PipelineContext $context): PipelineResult
    {
        /** @var BuildContext $context */
        $startTime = microtime(true);
        $result = new PipelineResult();
        $result->module = $this->name();

        try {
            $path = $context->latestDir . '/install.sql';
            $historyPath = $context->historyDir . '/install.sql';

            $sql = "-- AI Analysis Engine: Knowledge Pack SQL Export\n";
            $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";

            $sql .= "BEGIN;\n\n";

            // 1. Dictionaries
            $sql .= "-- DICTIONARIES\n";
            $dictionaries = $context->pack->dictionaries->all();
            foreach ($dictionaries as $name => $items) {
                foreach ($items as $item) {
                    $escaped = addslashes((string)$item);
                    $sql .= "INSERT IGNORE INTO engine_dictionaries (name, item_value) VALUES ('$name', '$escaped');\n";
                }
            }
            $sql .= "\n";

            // 2. Rules
            $sql .= "-- RULES\n";
            foreach ($context->pack->rules as $rule) {
                $uid = addslashes($rule->uid);
                $lang = addslashes($rule->language);
                $analysis = addslashes($rule->analysis);
                $feature = addslashes($rule->feature);
                $val = addslashes($rule->value);
                $section = addslashes($rule->section);
                $vis = addslashes($rule->visibility);
                $conf = (float) $rule->confidence;
                $prio = (int) $rule->priority;
                
                $sql .= "INSERT IGNORE INTO engine_rules (uid, language, analysis, feature, feature_value, section, visibility, confidence, priority) ";
                $sql .= "VALUES ('$uid', '$lang', '$analysis', '$feature', '$val', '$section', '$vis', $conf, $prio);\n";
            }
            $sql .= "\n";

            // 3. Relationships (For dependency ordering, relations come after rules)
            $sql .= "-- RELATIONSHIPS\n";
            foreach ($context->pack->rules as $rule) {
                if (!empty($rule->relationships)) {
                    foreach ($rule->relationships as $relUid) {
                        $from = addslashes($rule->uid);
                        $to = addslashes($relUid);
                        $sql .= "INSERT IGNORE INTO engine_relationships (source_uid, target_uid) VALUES ('$from', '$to');\n";
                    }
                }
            }

            $sql .= "\nCOMMIT;\n";

            file_put_contents($path, $sql);
            copy($path, $historyPath);

            $result->success = true;

        } catch (\Exception $e) {
            $result->success = false;
            $result->errors[] = $e->getMessage();
        }

        $result->executionTime = microtime(true) - $startTime;
        return $result;
    }
}
