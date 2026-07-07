# Commerce Specification

The AI Knowledge Platform's architecture strictly separates deterministic inference from commercial logic. The engine outputs signals (insights, impact scores, recommendations), and the Application Layer (e.g., WordPress plugin) determines pricing, access controls, and commerce flows.

## 1. Commerce Boundaries

- **Inference Engine**: Responsible for computing truth. It executes the Knowledge Pack to identify features, rank them by impact, and signal upgrade opportunities. It knows **nothing** about price, currency, or payment gateways.
- **Application Layer**: Responsible for commerce. It reads the `InferenceResult`, checks the user's entitlements, processes payments, and decides which visual segments to unlock based on visibility levels.

## 2. Product Architecture

The platform does not sell monolithic "Reports". It sells **Products** and **Collections**.

- **Products**: Granular insights around specific life questions. Examples: "Career Analysis", "Marriage Timeline", "Business Success". This enables micro-purchasing and increases customer lifetime value (LTV).
- **Collections**: Bundles of cross-domain knowledge. Example: "The Marriage Collection" containing synthesized insights from Palm Reading, Numerology, and Astrology.

## 3. The Recommendation DTO

To power dynamic, personalized upsells without hardcoding business rules into the engine, the engine emits `Recommendation` DTOs as part of the final `InferenceResult`.

```json
{
  "type": "upgrade",
  "title": "Marriage Analysis",
  "reason": "Hidden relationship patterns detected.",
  "priority": 97,
  "visibility": "free",
  "estimatedValue": "Very High",
  "estimatedReadTime": "5 min"
}
```
The application layer catches this DTO, attaches pricing, and renders it as a highly personalized Call-To-Action (CTA).

## 4. Visibility & Upgrade Opportunities

The engine evaluates and ranks insights by an internal `ImpactScore`.
- The renderer displays the top-ranked insights (Highest Impact) as free highlights.
- The remaining high-impact insights are transformed into **Upgrade Opportunities**. Example: *"17 hidden career insights found. Confidence 97%."*

### Dynamic Premium Levels
- **FREE**: High-impact insights (teasers and core takeaways).
- **PLUS**: Basic explanations and expanded insights.
- **PRO**: Deep explanations, exact timelines, evidence, and remedies.
- **MASTER**: Consultant mode, execution traces, cross-domain synthesis, and "Why did we predict this?" logic.

## 5. Cross-Domain Upsells (The Flywheel)

The first purchase should organically lead to the next. The engine's recommendation system must suggest adjacent domains based on the specific signals detected in the current run.
- *Detected: Strong Business Line* → *Recommend: Business Success Report*
- *Detected: Weak Relationship Line* → *Recommend: Numerology Compatibility*

By generating deterministic recommendation signals, the engine powers an entire ecosystem of life guidance products from a single, unified inference core.
