# PalmAnnotationSchema 1.0

This document defines the immutable contract for the AI Knowledge Platform's computer vision annotation format. All providers (Gemini, GPT, MediaPipe, Human) must strictly output this JSON schema.

## 1. Versioning
- Format: `PalmAnnotationSchema 1.0`
- Any additive changes bump to 1.1. Breaking changes bump to 2.0.

## 2. Root Structure
```json
{
  "schema_version": "PalmAnnotationSchema 1.0",
  "hand": "left|right|unknown",
  "source": {
    "provider": "gemini|human|gpt",
    "model": "2.5-flash",
    "revision": "2026-07"
  },
  "image_quality": {
    "sharpness": 95,
    "noise": 5,
    "brightness": 80,
    "contrast": 70,
    "crop": "good",
    "rotation": 0,
    "shadow": 10,
    "reflection": 5
  },
  "features": []
}
```

## 3. Feature Structure
```json
{
  "feature_id": "uuid",
  "feature_revision": 1,
  "category_id": "CAT001",
  "category": "line",
  "type": "life_line",
  "status": "confirmed|possible|uncertain|not_detected",
  "visual_confidence": 0.95,
  "geometry_confidence": 0.90,
  "geometry": {
    "type": "polygon|polyline|point|rectangle",
    "coordinates": []
  },
  "bbox": [xmin, ymin, xmax, ymax],
  "evidence": ["continuous_curve", "strong_edge"],
  "attributes": {
    "depth": "deep",
    "continuity": "solid"
  }
}
```

## 4. Evidence
Tokens must be selected strictly from `ObservationToken.md`. Free text is prohibited.
