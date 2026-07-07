# Product Strategy

The commercial success of the AI Knowledge Platform is built on a simple premise: **The engine always produces 100% of the knowledge. The renderer decides what to display based on the user's access tier.**

## 1. The Golden Rule

> **The free report should answer one question: *"Is this worth buying?"***
>
> **The premium report should answer every question the user came to ask.**

## 2. Rendering Tiers

The Inference Engine computes the full `InferenceResult` containing every rule match, confidence score, and relationship. Visibility is controlled at the rendering layer via rule visibility tags.

### Public (Free)
- **Goal**: Build trust, generate curiosity, and prove the engine's capability.
- **Content**: Basic information, a high-level score, and the three strongest traits.
- **Curiosity Hooks**: Explicitly quantify hidden value. Example: *"Your palm contains 63 observations, 18 predictions, and 11 remedies. Unlock your complete report."*

### Preview
- **Goal**: Tease specific premium sections without revealing the details.
- **Content**: Blurred sections or locked icons indicating categories like Career, Marriage, or Money. Example: *"🔒 Detailed compatibility timing available in Premium."*

### Premium (Paid)
- **Goal**: Deliver the ultimate customer value and a breathtaking report.
- **Content**: Everything. Deep insights across all categories (Career, Marriage, Money, Health), exact timelines, remedies, and a beautifully generated 25–40 page PDF.
- **Explainability**: "Why did we predict this?" expanding to show the evidence chain and confidence score.

### Professional (B2B / Consultant)
- **Goal**: Provide diagnostic tools for certified domain experts.
- **Content**: Full execution trace, rule indices, raw evidence confidence bounds, and pipeline analytics.

## 3. The Knowledge Moat
AI models (Gemini, OpenAI) are commodities. The platform's true moat is the **Knowledge Pack**. A proprietary, versioned heuristic engine containing tens of thousands of rules, relationships, and remedies cannot be trivially replicated by an LLM wrapper.

## 4. Future Upsells (Cross-Domain Reasoning)
Because the engine is domain-agnostic, future product expansion comes from combining Knowledge Packs:
- **Base**: Palm Reading
- **Upsell**: Palm Reading + Numerology (Cross-domain reasoning)
- **Ultimate**: Master Life Analysis (Palm + Numerology + Astrology)

By loading multiple compiled Knowledge Packs into a single inference pipeline, the platform can generate uniquely synthesized insights that generic AI systems cannot replicate.
