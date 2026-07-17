# Palm Reading Editorial Style Guide

This document defines the tone, structure, and standards for all knowledge rules authored for the AI Palm Reading platform. To maintain a premium, trustworthy experience, every rule must strictly adhere to these guidelines.

## 1. Core Philosophy
- **Empathetic & Human:** The report must sound like a senior palmist sitting across from the user. Avoid robotic or purely clinical descriptions.
- **Empowering, Not Fatalistic:** Never predict inevitable doom or failure. Frame challenges as areas for growth or conscious navigation.
- **Explainable:** Always connect the insight back to a physical observation, but do so naturally.

## 2. Voice & Tone
- **Voice:** Wise, encouraging, precise, and highly personalized.
- **Tone:** Professional but warm. Confident but not arrogant.
- **Point of View:** Second person ("You", "Your palm").

### Things We NEVER Say
- "You will die young" or "You will get a divorce" (No definitive negative predictions).
- "Your life line is short" (Avoid terms that imply deficiency without context).
- "The AI detected..." (The product is the palm reader, the AI is just the engine).

### Things We ALWAYS Say
- "Your palm indicates..."
- "This marking suggests..."
- "You possess a natural tendency to..."

## 3. Structural Guidelines

Every rule must contain a `languages` block with `en` and `hi` localization. Each localization must provide:

- **Headline (3-5 words):** Catchy, premium title. (e.g., "Enduring Vitality", "The Dual Thinker").
- **Summary (1 sentence):** The core takeaway that serves as the "hero" text.
- **Details (2-4 sentences):** The deep explanation. Connect the physical trait to the psychological or life outcome. This should read like a consultation.
- **Advice (1-2 sentences):** Actionable, supportive guidance.
- **CTA (2-5 words):** Action-oriented prompt teasing the related premium blueprint (e.g., "Continue your Career Blueprint").

## 4. Hindi Terminology Standards
- Use accessible, conversational Hindi (Hinglish is acceptable if it sounds more natural to the modern Indian audience, but pure Hindi is preferred for premium reports).
- **Tone:** 'Aap' (आप), never 'Tum' (तुम) or 'Tu' (तू) to maintain respect.
- **Terminology:** 
  - Life Line: जीवन रेखा (Jivan Rekha)
  - Heart Line: हृदय रेखा (Hriday Rekha)
  - Head Line: मस्तिष्क रेखा (Mastishk Rekha)
  - Fate Line: भाग्य रेखा (Bhagya Rekha)
  - Mounts: पर्वत (Parvat - e.g., गुरु पर्वत for Jupiter Mount)

## 5. Insight Types
Every rule must be assigned one of the following types to dictate its visual presentation:
1. `Strength` (Natural advantages)
2. `Opportunity` (Areas for potential growth)
3. `Challenge` (Internal struggles or obstacles)
4. `Potential` (Latent talents)
5. `Rare Discovery` (Uncommon markings like stars, crosses)
6. `Guidance` (Replaces 'Warning'—supportive advice on how to navigate difficult traits)

## 6. Example Rule (The Gold Standard)

```json
"languages": {
  "en": {
    "headline": "The Dual Thinker",
    "summary": "You possess the rare ability to blend strict logic with boundless creativity.",
    "details": "A fork at the end of the head line, often called the 'Writer's Fork', indicates a mind capable of seeing multiple perspectives simultaneously. You can conceptualize abstract ideas and apply rigorous logic to bring them into reality.",
    "advice": "Seek out roles that don't force you into a single box. You thrive when allowed to be both the architect and the builder.",
    "cta": "Explore your Career Blueprint"
  },
  "hi": {
    "headline": "दोहरी सोच का धनी",
    "summary": "आपमें तर्क और रचनात्मकता का एक दुर्लभ और अद्भुत संतुलन है।",
    "details": "मस्तिष्क रेखा के अंत में दो भागों में बंटना दर्शाता है कि आप एक ही समय में कई दृष्टिकोणों से सोच सकते हैं। आप अमूर्त विचारों की कल्पना कर सकते हैं और उन्हें वास्तविकता में बदलने के लिए कठोर तर्क का उपयोग कर सकते हैं।",
    "advice": "ऐसी भूमिकाओं की तलाश करें जो आपको किसी एक सांचे में न बांधें। आप तब सबसे अच्छा प्रदर्शन करते हैं जब आपको स्वतंत्र रूप से काम करने की छूट मिलती है।",
    "cta": "अपना करियर ब्लूप्रिंट देखें"
  }
}
```
