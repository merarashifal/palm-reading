# Versioning Policy

The AI Knowledge Platform enforces strict versioning to ensure deterministic execution, backward compatibility for stored execution traces, and consistent integration rules for providers and consumers.

## 1. Semantic Versioning

The core engine adheres to strict Semantic Versioning (`MAJOR.MINOR.PATCH`):
- **MAJOR**: Incompatible API changes, significant architectural shifts, or breaking changes in the normalization DTO schema.
- **MINOR**: Addition of functionality in a backwards compatible manner (e.g., adding a new stage to the Inference Pipeline).
- **PATCH**: Backwards compatible bug fixes (e.g., edge-case handling in rule compilation).

## 2. Knowledge Pack Compatibility

Knowledge Packs are independently versioned and structurally decoupled from the Engine.
- A Knowledge Pack explicitly defines a `requiredEngineVersion` (e.g., `^0.8.0`).
- The Knowledge Engine MUST reject any pack that demands an incompatible major version or a higher minor version than currently running.
- Updates to Knowledge Packs (e.g., tweaking string copy or dictionary elements) bump the Pack's MINOR or PATCH version and are guaranteed not to break Inference logic.

## 3. Replay Compatibility

Because a `ReplayPackage` serves as a historical execution artifact, it MUST include versions at the time of creation:
- `engineVersion`
- `knowledgePackVersion`
- `analysisDefinitionVersion`
- `providerVersion`
- `schemaVersion`

A Replay test will warn (or optionally fail) if executed on an engine environment that deviates from the recorded `schemaVersion` or `engineVersion`, ensuring the diagnostic replay exactly reflects the historical state.

## 4. Provider Compatibility

Providers operate as separate modules or packages.
- A Provider declares the `schemaVersion` of the `NormalizedFeatureCollection` it is capable of producing.
- If the Platform upgrades its `schemaVersion` in a breaking way, the Provider certification is instantly marked "Deprecated" until it implements the new schema.

## 5. Migration Rules

When performing MAJOR engine upgrades:
1. Old execution traces (`ReplayPackage`) are preserved but may require a documented migration script to be loadable in new Observability dashboards.
2. Knowledge Packs require recompilation against the new target architecture.
3. Consumers (like WordPress) are insulated via standard REST APIs, which version externally (e.g., `/api/v1/analyze`).
