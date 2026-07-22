<?php

namespace AIAnalysisEngine\Contracts;

use AIAnalysisEngine\Knowledge\Context\KnowledgeContext;
use AIAnalysisEngine\Knowledge\Validator\ValidationResult;

interface ValidatorInterface {
    public function validate(KnowledgeContext $context): ValidationResult;
}
