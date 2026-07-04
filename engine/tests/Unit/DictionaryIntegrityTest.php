<?php

namespace AIAnalysisEngine\Tests\Unit;

use PHPUnit\Framework\TestCase;
use AIAnalysisEngine\Knowledge\Registry\KnowledgeRegistry;

class DictionaryIntegrityTest extends TestCase {
    
    public function testDictionariesAreValidJson() {
        $registry = new KnowledgeRegistry();
        
        // Assuming dictionaries are located in the database/knowledge/dictionary path
        $dictPath = dirname(dirname(dirname(__DIR__))) . '/database/knowledge/dictionary';
        
        if (!is_dir($dictPath)) {
            $this->markTestSkipped("Dictionary directory not found at $dictPath");
        }
        
        $files = glob($dictPath . '/*.json');
        foreach ($files as $file) {
            $data = $registry->loadDictionary($file);
            $this->assertIsArray($data, "Dictionary $file should decode to an array");
        }
    }
}
