# Deprecation Policy

To ensure long-term stability for Knowledge Packs, Providers, and Consumers, the AI Knowledge Platform adheres to the following strict deprecation lifecycle:

## Version Semantics
- **Major Versions** (`v2.0.0`): May remove previously deprecated APIs or introduce breaking schema changes to Knowledge Packs or DTOs.
- **Minor Versions** (`v1.1.0`): May add new APIs, pipelines, or properties in a strictly backwards-compatible manner.
- **Patch Versions** (`v1.0.1`): Only for bug fixes; never changes behavior or signatures.

## The Deprecation Lifecycle
1. **Notice**: When a Public API or Contract is marked for removal, it is annotated with `@deprecated` in the code and added to the Release Notes.
2. **Grace Period**: Deprecated APIs **must** remain functional for at least **2 Minor versions**.
3. **Removal**: The API may only be fully removed in the next **Major version**.

*Example: An API deprecated in v1.2 must remain in v1.3 and v1.4. It can be safely removed in v2.0.*
