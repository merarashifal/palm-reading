<?php

namespace AIAnalysisEngine\Knowledge\Validator;

use AIAnalysisEngine\Knowledge\Context\KnowledgeContext;

interface ValidatorInterface {
    public function validate(KnowledgeContext $context): ValidationResult;
}
