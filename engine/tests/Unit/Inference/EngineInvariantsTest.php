<?php

namespace AIAnalysisEngine\Tests\Unit\Inference;

use PHPUnit\Framework\TestCase;
use AIAnalysisEngine\Tests\Fixtures\FixtureFactory;
use AIAnalysisEngine\Inference\InferenceContext;
use AIAnalysisEngine\Inference\DTO\PermissionContext;
use AIAnalysisEngine\Inference\DTO\EngineConfiguration;
use AIAnalysisEngine\Inference\Pipeline\InferenceFactory;
use AIAnalysisEngine\Inference\DTO\CandidateStatus;
use AIAnalysisEngine\Inference\DTO\Candidate;

class EngineInvariantsTest extends TestCase
{
    private InferenceContext $context;

    protected function setUp(): void
    {
        $knowledge = FixtureFactory::createSmall();
        $permission = new PermissionContext();
        $config = new EngineConfiguration();
        $config->allowExperimental = true;

        $rawPayload = [
            ['analysis' => 'life_line', 'feature' => 'life_line', 'value' => 'broken', 'confidence' => 0.9]
        ];

        $this->context = new InferenceContext($rawPayload, $knowledge, $permission, $config);
        $pipeline = InferenceFactory::createPipeline();
        $pipeline->execute($this->context);
    }

    public function testPipelineNeverReturnsNullCandidates()
    {
        foreach ($this->context->candidates as $candidate) {
            $this->assertNotNull($candidate, "Candidate collection contains null.");
        }
    }

    public function testEveryActiveCandidateHasConfidence()
    {
        foreach ($this->context->candidates->active() as $candidate) {
            $this->assertNotNull($candidate->confidence, "Active candidate lacks confidence.");
            $this->assertGreaterThan(0.0, $candidate->confidence->final, "Active candidate final confidence is zero.");
        }
    }

    public function testEveryActiveCandidateHasEvidence()
    {
        foreach ($this->context->candidates->active() as $candidate) {
            $this->assertGreaterThan(0, count($candidate->evidence->all()), "Active candidate has no evidence.");
        }
    }

    public function testEveryEvidenceReferencesExistingFeature()
    {
        // For derived evidence, this might not map directly to a hydrated feature. 
        // We will only check non-derived evidence.
        foreach ($this->context->evidence as $evidence) {
            if (!$evidence->isDerived) {
                // Find matching feature
                $found = false;
                foreach ($this->context->features as $feature) {
                    if ($feature->feature === $evidence->feature && $feature->value === $evidence->value) {
                        $found = true;
                        break;
                    }
                }
                $this->assertTrue($found, "Evidence {$evidence->feature} does not map to a base Feature.");
            }
        }
    }

    public function testEveryCandidateReferencesExistingRuleUID()
    {
        foreach ($this->context->candidates as $candidate) {
            $rule = $this->context->knowledge->rules->index->getByUid($candidate->rule->uid);
            $this->assertNotNull($rule, "Candidate references missing rule UID: {$candidate->rule->uid}");
        }
    }

    public function testInferenceResultContainsNoDuplicateItems()
    {
        foreach ($this->context->result->sections as $section) {
            $seenFeatures = [];
            foreach ($section->items as $item) {
                // Collect underlying evidence keys
                $keys = [];
                foreach ($item->evidence as $ev) {
                    $keys[] = $ev->feature . '::' . $ev->value;
                }
                sort($keys);
                $hash = implode('|', $keys);
                
                $this->assertArrayNotHasKey($hash, $seenFeatures, "Duplicate item detected in InferenceResult section.");
                $seenFeatures[$hash] = true;
            }
        }
    }

    public function testExecutionTraceDurationsAndMemoryArePositive()
    {
        $stages = $this->context->trace->getStages();
        $this->assertNotEmpty($stages, "Execution trace is empty.");

        foreach ($stages as $stage) {
            $this->assertGreaterThanOrEqual(0, $stage['duration_ms'], "Stage {$stage['stage']} has negative duration.");
            $this->assertGreaterThanOrEqual(0, $stage['memory_before'], "Stage {$stage['stage']} has negative memory_before.");
            $this->assertGreaterThanOrEqual(0, $stage['memory_after'], "Stage {$stage['stage']} has negative memory_after.");
            // memory_diff can theoretically be negative if GC runs, but typically shouldn't cause a failure for the test. We'll ensure peak is >= 0
            $this->assertGreaterThanOrEqual(0, $stage['peak_memory'], "Stage {$stage['stage']} has negative peak_memory.");
        }
    }
}
