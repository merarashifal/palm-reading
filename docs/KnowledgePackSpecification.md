# Knowledge Pack Specification

The Knowledge Pack is the true core product of the AI Knowledge Platform. It contains 100% of the domain reasoning, terminology, and heuristics.

## Structure
A Knowledge Pack is a versioned bundle containing:
- **Rules**: Declarative heuristics that map abstract AI features to specific meanings (e.g., "Line > 5cm = Long Life").
- **Relationships**: Graph links defining how features interact with or negate one another.
- **Dictionaries**: Localized strings and definitions for all domain terms.
- **Remedies**: Actionable advice or solutions linked to specific rule outcomes.
- **Manifest**: Metadata declaring the pack's version, domain, and compatible Engine version.

## Authoring Philosophy
Knowledge Packs must be written by Subject Matter Experts (SMEs), not developers. They do not contain code. They are authored in structured formats (initially JSON/Markdown, eventually via the Knowledge Authoring Studio UI).

## Compilation
Knowledge Packs are never executed in their raw format. They must pass through the **Compiler Engine**, which:
1. Validates structural and semantic integrity.
2. Checks for logical conflicts or unreachable rules.
3. Compresses the heuristics into a binary/indexed format (e.g., SQLite) optimized for sub-millisecond querying by the Inference Engine.
