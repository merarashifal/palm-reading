# Observability Specification

The Observability Specification defines how the AI Knowledge Platform records, structures, and manages the execution lifecycle of any operation. It transforms a "black box" AI inference into a fully deterministic, queryable, and replayable execution.

## 1. Trace Lifecycle
Every session execution is captured within an overarching `ExecutionTrace`. The lifecycle of tracing operates strictly parallel to execution.

1. **Initialization**: A unique `sessionId`, `requestId`, and `correlationId` are assigned.
2. **Stage Execution**: Each platform stage explicitly implements a `StageTrace` capturing discrete I/O boundaries.
3. **Serialization**: Upon completion (or failure), the `ExecutionTrace` is serialized into an immutable JSON artifact.
4. **Retention**: The JSON artifact is saved into `storage/replay/trace/` for persistence. Only the generated `trace_id` may be persisted in any related relational databases.

## 2. Stage Recording
Every stage (Provider, Inference, Validation) must adhere to a standardized contract via the `StageTrace` interface.

**Required Properties:**
- `stage` (string): The identifier of the stage (e.g., "Normalization").
- `start` (float): Microsecond timestamp.
- `end` (float): Microsecond timestamp.
- `duration` (float): Milliseconds.
- `status` (string): e.g., "success", "failed".
- `itemsIn` (int): Number of input entities.
- `itemsOut` (int): Number of output entities.
- `memoryBefore` (int): Bytes.
- `memoryAfter` (int): Bytes.
- `reason` (string): Any diagnostic reason for failure or anomalous exit.

## 3. Correlation IDs
To enable cross-boundary debugging, every trace MUST inject:
- `sessionId`: Identifies a continuous user interaction (e.g., "User's session over multiple prompts").
- `requestId`: Identifies a unique HTTP/API request hitting the backend.
- `correlationId`: Identifies the logical flow traversing Provider, Inference, and Generation, ensuring one end-to-end trace string.

## 4. Replay System
The Replay Package acts as the singular source of truth for an execution dump. It must encapsulate exactly what occurred without mutation.

**Immutable Contents:**
- `AnalysisDefinition`
- `PromptDocument`
- `Request` (raw)
- `Response` (raw)
- `NormalizedFeatures`
- `InferenceResult`
- `ExecutionTrace`

Replays include strict software versions (`engineVersion`, `knowledgePackVersion`, etc.) to answer "What software produced this?".

## 5. Sampling Strategy
As traffic scales, the platform must avoid recording 100% of successful payloads. The sampling strategy is divided into four tiers:

1. **Always**: For the initial launch and beta testing phase.
2. **Sampled**: E.g., 5% of all standard, successful requests are stored.
3. **Errors Only**: 100% of requests that result in a platform failure, provider failure, or validation constraint breach.
4. **Certification Mode**: 100% recording active for Golden test runs and provider audits.
