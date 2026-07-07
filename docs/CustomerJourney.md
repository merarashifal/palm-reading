# Customer Journey Specification

The AI Knowledge Platform's commercial success relies on a carefully orchestrated customer journey. Every screen has exactly ONE objective designed to move the customer to the next state, transforming a one-time visitor into a lifetime collector of Life Intelligence.

## 1. The Journey Funnel

Visitor 
↓ Lands on page 
↓ Reads benefits 
↓ Uploads Palm 
↓ AI analyzes 
↓ Inference runs 
↓ Free Report 
↓ Curiosity 
↓ Purchase 
↓ Premium Report 
↓ Recommendation 
↓ Next Product 
↓ Lifetime Customer

## 2. Screen Objectives

### Landing Page
- **Goal**: Upload Palm
- **Focus**: Nothing else. A single, clear Call-To-Action (CTA).

### Upload Page
- **Goal**: Reduce fear.
- **Focus**: "This takes less than 30 seconds." Provide immediate reassurance regarding privacy and speed.

### Processing Screen
- **Goal**: Increase anticipation.
- **Focus**: Do not use a generic "Loading..." spinner. Show dynamic progress:
  - ✓ Palm image verified
  - ✓ 63 features detected
  - ✓ Mapping life line
  - ✓ Evaluating career indicators
  - ✓ Building confidence model
  - ✓ Preparing personalized report

### Free Report
- **Goal**: Build trust and curiosity. Never sell immediately.
- **Focus**: Highlight unexpected value. *"Wow. Your Career score is unusually high. Business ability is one of your strongest strengths. Relationship analysis also revealed several uncommon patterns."*

### Premium Screen (The Upsell)
- **Goal**: Drive conversion through quantified mystery.
- **Focus**: Do not say "Unlock Premium". Say: *"Your analysis contains 63 observations, 41 hidden insights, 11 remedies, 7 timeline predictions, and 3 rare patterns."* 

### Payment Success
- **Goal**: Celebrate the purchase.
- **Focus**: *"Your Complete Life Blueprint is ready."* Do not use generic transactional text like "Payment Successful".

### Premium Report
- **Goal**: Overdeliver.
- **Focus**: Every section, timeline, and explanation must make the user think, *"This was worth paying for."* Show evidence via the "Why did we predict this?" feature.

### End of Report
- **Goal**: Drive the next interaction (The Flywheel).
- **Focus**: Never end with "Thank You". End with a dynamic recommendation: *"Recommended Next Step: Business Success Analysis, because your palm shows exceptional entrepreneurial indicators."*

## 3. Customer State

The platform tracks `CustomerState` rather than just a generic `User` model. Renderers and CTAs dynamically adjust based on this state to avoid repetitive messaging.

1. **Visitor**: First interaction.
2. **Analyzed**: Has received a free report.
3. **Interested**: Has clicked on premium teasers.
4. **Purchased**: Completed first transaction.
5. **Returning**: Consuming past reports.
6. **Collector**: Has purchased multiple products (e.g., Palm + Numerology).
7. **Professional**: A B2B consultant using the tools for their own clients.

## 4. Vision: Life Intelligence
The long-term goal is not to sell one "Palm Reading", but to build a continuously updating **"My Life Intelligence Profile"**. The customer journeys from Palm → Numerology → Birth Chart → Face → Name → Annual Forecasts, creating a permanent, high-retention relationship.
