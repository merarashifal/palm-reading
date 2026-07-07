# AI Inference Engine Specification

## 1. Purpose
The Inference Engine transforms normalized AI observations into deterministic, explainable human insights. It is the core reasoning layer of the platform, built to support Palm Reading, Face Reading, Numerology, Vastu, Horoscope, and unified multi-domain reasoning simultaneously.

## 2. Philosophy
This engine represents the constitution of the AI Platform:
- The engine **never** invents facts.
- The engine **never** generates text dynamically (no LLM hallucinations).
- The engine **only** reasons over validated, compiled knowledge.
- Every conclusion must be **explainable**.
- Every prediction must be **traceable** back to the exact evidence that triggered it.
- **Every inference must be reproducible from the same input and the same knowledge pack.**

## 3. Input Boundary
The engine accepts **strictly** normalized JSON payloads produced by the upstream Gemini Vision layer. These are immediately converted into strongly-typed `Feature` DTOs (e.g., `FeatureCollection`). The engine never reasons over raw arrays.
- No images are passed to the engine.
- No textual prompts are passed.
- The engine knows nothing of WordPress or user sessions.

## 4. Knowledge Model
The engine executes reasoning against the `CompiledKnowledgePack`—a deterministic, O(1) indexed, in-memory representation of domain knowledge. The engine traverses `RuleIndexes`, resolves `Relationships`, and fetches localized `Translations` instantly without filesystem or database lookups.

## 5. Reasoning Model
The engine does not execute procedural code to guess outcomes. It reasons through a strictly ordered sequence:
`Observation -> Evidence -> Derived Evidence -> Candidate Rules -> Ranking -> Resolution -> Inference -> Result`

## 6. Evidence & Derived Evidence
Every observation is wrapped into an **Evidence** object before processing.
*Example:* 
```php
Evidence {
  feature: "broken_life_line",
  confidence: 0.94,
  weight: 85
}
```
If a user asks "Why did we predict this?", the engine traces the final insight back to this Evidence. 
Additionally, traversing the graph can create **Derived Evidence** (e.g., `Evidence A + Evidence B -> Derived Evidence C`). This makes the engine recursive without actual recursion.

## 7. Candidate Rules & Reasons
Matches do not immediately become predictions. They are wrapped in a **Candidate** object:
```php
Candidate {
  rule: CompiledRule,
  evidence: EvidenceCollection,
  score: float,
  confidence: ConfidenceResult,
  reason: Reason (e.g., MATCHED, OVERRIDDEN, MERGED, ESCALATED),
  status: string (ACTIVE, DISCARDED, etc.)
}
```

## 8. Ranking
The **Ranker** produces numerical scores for Candidates, but does not choose the winner. The score considers:
- **Priority**: Domain-expert assigned rule weights.
- **Evidence Weight**: Initial strength of the feature.
- **Relationship Bonus**: Synergistic graph effects.
- **Confidence Bonus**: AI certainty.

## 9. Conflict Resolution
The **ConflictResolver** evaluates Candidate scores and applies strict strategies when rules overlap:
- **Override**: A higher-scoring rule completely replaces a lower one.
- **Merge**: Non-contradictory rules append their insights.
- **Discard**: Rules failing permission/confidence thresholds are dropped.
- **Escalate**: Conflicting rules of equal priority trigger a fallback generalization rule.

## 10. Relationship Reasoning
The engine traverses knowledge as a graph to trigger derived evidence. For example: `Life Line + Fate Line -> Career Success`. This unified reasoning can also bridge multi-domain inputs (`Palm + Numerology -> Unified Evidence`).

## 11. Confidence Scoring
The engine explicitly tracks confidence via a `ConfidenceResult` object, cleanly separating sources:
- **AI Confidence**: Certainty of the Vision model (`0.83`).
- **Inference Confidence**: Domain knowledge strength (`0.95`).
- **Relationship Confidence**: Modifiers from graph synergies.
- **Final Confidence**: Deterministic aggregate.

## 12. Permission Layer (Visibility)
Visibility acts as a generic Permission Layer scaling across contexts:
- `Public` (Free teaser)
- `Premium` (Paid)
- `Internal` (Admin diagnostics)
- `Experimental` (Beta testing)

## 13. Output: Inference Result
The output is a minimal `InferenceResult` DTO. It does NOT store raw predictions, but organizes by `sections`:
```php
InferenceResult {
  sections: [
    Section {
      items: [
        Item { evidence, confidence, translations }
      ]
    }
  ],
  overall_confidence: 0.89,
  visibility_context: "Premium"
}
```
**Architecture Guarantee:** Renderers *only* consume the `InferenceResult`. They never touch `CompiledRule` or `CompiledKnowledgePack` directly, preventing coupling.

## 14. Execution Trace
For instant debuggability, every pipeline stage logs its execution footprint to the `ExecutionTrace` inside the `InferenceContext`.
*Example:* `FeatureMatcher (12ms) -> EvidenceBuilder (4ms) -> RelationshipExpander (2ms)`

---

## 15. The Inference Pipeline Architecture
The engine uses the universal Pipeline framework to orchestrate reasoning. Every stage receives a single, unified `InferenceContext` containing the pack, collections, permissions, and configuration.

1. **FeatureMatcher**: Parses raw inputs into `FeatureCollection`.
2. **EvidenceBuilder**: Constructs baseline `EvidenceCollection`.
3. **RelationshipExpander**: Graph traversal to produce Derived Evidence before matching.
4. **RuleMatcher**: Executes O(1) lookups to find matches.
5. **CandidateBuilder**: Groups matches into the unfiltered `CandidateCollection`.
6. **Ranker**: Scores candidates individually.
7. **ConflictResolver**: Evaluates scores to Override/Merge/Discard/Escalate candidates.
8. **ConfidenceResolver**: Calculates the final multi-dimensional `ConfidenceResult`.
9. **InferenceAssembler**: Packages surviving active candidates into the strict `InferenceResult`.
