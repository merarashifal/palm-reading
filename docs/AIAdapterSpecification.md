# AI Adapter Specification

## 1. The Core Philosophy

The AI Adapter layer is the strict boundary between external intelligence (Computer Vision, Generative AI) and internal intelligence (The Inference Engine). 

To prevent architectural drift and guarantee deterministic reasoning, the following constitutional laws apply to every AI Provider integration:

1. **AI Providers detect features.** They only answer: *"Here is what I detected."*
2. **The Engine performs reasoning.** Providers *never* perform reasoning or evaluate complex conditions.
3. **Providers never generate predictions.** They only emit observations.
4. **Providers never know the knowledge pack or the domain.** They are completely ignorant of what "Palm Reading" or "Face Reading" is. Domain knowledge exists solely in the `AnalysisDefinition`.
5. **Providers are replaceable.** Gemini, OpenAI, and MediaPipe must all normalize into the exact same DTOs.
6. **Data-Driven Execution.** The pipeline relies entirely on the `AnalysisSession` (powered by an `AnalysisDefinition`). There are no hardcoded string domains like "Palm" inside the providers.

## 2. The Adapter Pipeline Lifecycle

The AI Adapter is designed as a pipeline, strictly isolating HTTP/API logic from domain knowledge normalization.

```
InputArtifact (Image, PDF, Audio)
      │
      ▼
ImagePreprocessor (Resize, Compress, Enhance)
      │
      ▼
RequestBuilder (Formats payload for specific provider: inline_data, image_url, etc.)
      │
      ▼
ProviderClient (Executes HTTP call)
      │
      ▼
ResponseParser (Extracts JSON/Raw data from provider response)
      │
      ▼
ConfidenceCalibrator (Maps provider-specific confidence to a standardized engine baseline)
      │
      ▼
FeatureNormalizer (Statelessly maps raw JSON to NormalizedFeature objects)
      │
      ▼
NormalizedFeatureCollection (The final boundary output)
```

After the `NormalizedFeatureCollection` is produced, it is passed to the **Validation Pipeline** and finally to the **Inference Pipeline**. The AI Adapter is completely detached from those downstream processes.

## 3. The Normalized Feature Contract

Every AI Provider must eventually emit a `NormalizedFeatureCollection` containing `NormalizedFeature` DTOs. 

This contract is immutable:

```json
{
  "id": "feat_12345",
  "analysis": "palm",
  "feature": "life_line",
  "value": "broken",
  "confidence": 0.91,
  "weight": 50,
  "source": "gemini",
  "provider": "google",
  "provider_version": "gemini-1.5-pro",
  "coordinates": {
    "type": "polygon",
    "points": [ {"x":0.4, "y":0.1}, {"x":0.45, "y":0.2} ]
  },
  "metadata": {
    "latency_ms": 1200
  }
}
```

> **Note**: `confidence` is the probability the feature exists. `weight` is its anatomical or structural importance. They are fundamentally different.

## 4. Bounding Regions (Coordinates)

Coordinates must be future-proofed to support multiple modalities (Faces, Palms, Documents). We use the `BoundingRegion` DTO.

Supported types:
- `polygon`: E.g., Palm shapes.
- `rectangle`: E.g., Bounding boxes for documents.
- `landmark`: E.g., Point maps for Face Mesh.

## 5. Domain Knowledge Isolation

Domain logic (e.g., mapping a raw Google Gemini label like "line_broken_center" to our internal `life_line = broken`) belongs exclusively inside the `FeatureNormalizer`. 

The `ProviderClient` and `ResponseParser` must **never** contain domain logic. A `GeminiVisionAdapter` simply returns a `ProviderResponse` containing the raw output. The normalizer handles the translation.

## 6. Multi-Provider Consensus

The architecture supports parallel provider execution. In future iterations, the engine may request insights from multiple providers simultaneously:

```
Gemini + OpenAI Vision -> Consensus Layer -> Validation -> Inference
```

All consensus logic (Majority, Weighted, Priority) must exist within the `AI\Consensus` namespace and strictly evaluate collections of `NormalizedFeature` DTOs.
