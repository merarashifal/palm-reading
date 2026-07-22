<?php

namespace AIAnalysisEngine\Tests\Fixtures;

use AIAnalysisEngine\Knowledge\Compiled\CompiledKnowledgePack;
use AIAnalysisEngine\Knowledge\Compiled\CompiledRule;
use AIAnalysisEngine\Knowledge\Compiled\CompiledRuleCollection;
use AIAnalysisEngine\Knowledge\Compiled\CompiledDictionaryCollection;

class FixtureFactory
{
    /**
     * Generates a realistic CompiledKnowledgePack containing the specified number of rules.
     * This avoids having to write JSON and compile it just to test inference.
     *
     * @param int $size Number of rules to generate
     * @return CompiledKnowledgePack
     */
    public static function create(int $size = 5000): CompiledKnowledgePack
    {
        $pack = new CompiledKnowledgePack();

        // 1. Manifest
        $pack->manifest = [
            'knowledge_pack_version' => '1.0',
            'engine_version' => '0.8.0',
            'domains' => ['palm', 'face', 'unified']
        ];

        // 2. Metadata
        $pack->metadata = [
            'build' => [
                'knowledge_pack_version' => '1.0',
                'engine_version' => '0.8.0',
                'compiled_at' => date('c')
            ]
        ];

        // 3. Dictionaries
        $pack->dictionaries = new CompiledDictionaryCollection();
        // Just mock some basic stuff
        
        // 4. Rules
        $rules = new CompiledRuleCollection();
        $analyses = ['life_line', 'heart_line', 'fate_line', 'head_line', 'mounts', 'signs'];
        $values = ['long', 'short', 'broken', 'chained', 'deep', 'faint', 'starred'];
        $visibilities = ['public', 'premium', 'internal'];

        for ($i = 1; $i <= $size; $i++) {
            $rule = new CompiledRule();
            $rule->uid = "RULE_" . str_pad((string)$i, 6, '0', STR_PAD_LEFT);
            $rule->language = 'en';
            
            // Distribute domains/analyses generically
            $analysis = $analyses[array_rand($analyses)];
            $rule->analysis = $analysis;
            
            $rule->feature = $analysis; // e.g. "life_line"
            $rule->value = $values[array_rand($values)];
            $rule->section = 'general';
            $rule->visibility = $visibilities[array_rand($visibilities)];
            $rule->confidence = (float) (rand(50, 100) / 100);
            $rule->priority = rand(10, 100);
            
            // Random relationships (10% chance)
            if (rand(1, 10) === 1 && $i > 1) {
                $target = rand(1, $i - 1);
                $rule->relationships[] = "RULE_" . str_pad((string)$target, 6, '0', STR_PAD_LEFT);
            }
            
            $rules->add($rule);
        }

        // Force indexes to build since we skipped compiler
        $rules->rebuildIndexes();

        $pack->rules = $rules;
        return $pack;
    }

    public static function createSmall(): CompiledKnowledgePack
    {
        return self::create(100);
    }

    public static function createMedium(): CompiledKnowledgePack
    {
        return self::create(5000);
    }

    public static function createLarge(): CompiledKnowledgePack
    {
        return self::create(50000);
    }

    public static function createMassive(): CompiledKnowledgePack
    {
        return self::create(250000);
    }
}
