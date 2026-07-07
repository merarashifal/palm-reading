# Public API Specification

The AI Knowledge Platform guarantees strict backward compatibility for its public APIs across all `MINOR` and `PATCH` versions.

## 1. Stable (Public Contract)
These components are safe for external consumption and will not change without a `MAJOR` version bump.

- **DTOs**:
  - `InferenceResult`
  - `NormalizedFeature` / `FeatureCollection`
  - `ExecutionTrace`
  - `StageTrace`
  - `ReplayPackage`
- **Contracts**:
  - `ProviderContract` (Interfaces governing AI adapters)
  - `PipelineContract` (Interfaces for Pipeline Stages)
- **Data Formats**:
  - Knowledge Pack JSON/Markdown Schema
  - Compiled Knowledge Pack Binary Schema (SQL/SQLite)

## 2. Internal (Private Contract)
These components are for engine-internal use only. Their signatures and behaviors may change in any `MINOR` or `PATCH` release.

- `RuleIndex`
- Internal `Factories`
- Internal `Collections` (unless explicitly returned by a Public API)
- `Builders`
- `Optimizers`
- `Compilers`

Do not rely on Internal APIs when building external Renderers, Consumers, or Providers.
