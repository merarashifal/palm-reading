# MeraRashifal AI Beta Launch Playbook (v1.0.0-beta)

This document is the operational playbook for the Beta launch. It shifts our focus entirely from engineering to product validation, user behavior, and business conversion.

## 1. Beta Objective
To validate that users are willing to upload a palm image, consume a free analysis, and exchange their contact information (Name, Mobile, DOB) to unlock their complete Personal Palm Blueprint. The ultimate goal is to generate our first 1,000 highly qualified leads.

## 2. Success Criteria
The beta is considered a success when the following loop is completed organically:
1. User uploads a palm image successfully.
2. Engine generates analysis once (Target: < 20 seconds).
3. User reads the free report.
4. User feels enough curiosity to click "Unlock".
5. User enters Name + Mobile + DOB.
6. Premium report unlocks instantly.
7. User downloads PDF or shares the report.

## 3. The Customer Journey
This is the exact flow every visitor must experience.

### Step 1: Landing Page
- **Objective:** Convince visitor to upload.
- **Copy:** "Unlock Your Personal Palm Blueprint. Discover what your palm reveals about your personality, career, relationships, hidden strengths and future potential."
- **Trust Elements:** "50+ Features Analyzed", "100% Free Preview", "No Registration Required".
- **Visuals:** High-quality screenshots of the premium report (people buy with their eyes).
- **FAQ:** 5 core questions (Is it free? How accurate? Which hand? How long? What will I get?).

### Step 2: Upload
- **Frictionless Form:** Only ask for an optional Name, Language preference (English/Hindi), and the Palm Image. No mobile or DOB yet.

### Step 3: Processing
- **UX:** Engaging loading screen setting expectations. 
- **Copy:** "Our engine is analyzing Palm Quality, Major Lines, Rare Signs... This usually takes around 15 seconds."

### Step 4: Free Report (Curiosity Engine)
- **Hero:** "Hello Tushar, Your Palm Analysis is Ready" with a massive, punchy discovery statement (e.g., "You possess exceptional leadership potential.").
- **Trust Block:** Show Confidence %, Image Quality, and Features Identified.
- **Evidence:** "Why we believe this: ✓ Strong Life Line ✓ Deep Head Line".
- **Premium Blur (Curiosity Gap):** Show only 3 blurred cards ("Career Blueprint", "Relationship Pattern", "Lucky Years"). 
- **Lead Capture CTA:** "Complete Your Personal Profile to unlock your complete Palm Blueprint" (Ask for Name, Mobile, DOB).

### Step 5: Thank You & Permanent URL
- **Post-Capture:** "Thank you. Your complete blueprint has been prepared."
- **Actions:** View Full Report, Download PDF, Share.
- **Persistence:** Every report lives permanently at `merarashifal.com/report/PB-XXXXXX`.

## 4. Dashboards & Daily KPIs
We are operating with two distinct dashboards:

### Engineering Dashboard
- Total Uploads vs. Failures
- Average Latency (Target: < 20s)
- Gemini API Cost & Execution Time
- Image Quality Distribution
- System Errors

### Business Dashboard (The Focus)
- Total Visitors vs. Total Uploads (Conversion Rate)
- Completed Reports (Engine Success)
- Profile Completion % (Lead Gen Rate)
- Most Read Insight / Top Category
- Average Read Time
- Return Visitors
- Top Languages & Devices

## 5. QA Strategy (Structured Dataset)
We will test against a structured matrix of 100 images, not random uploads:
- **Quality:** 20 Excellent, 20 Good, 20 Average, 20 Poor, 20 Very Poor
- **Demographics:** 20 Left / 20 Right, 20 Female / 20 Male, 20 Elderly / 20 Young
- **Devices:** Android, iPhone, Chrome, Safari, Edge.

---

## 6. Known Issues
*(Log any bugs or UX issues discovered during the beta here)*
- [ ] TBD

## 7. Feedback Log
*(Log direct quotes or observed behaviors from the first 100 users)*
- [ ] TBD

## 8. Decisions Made from User Feedback
*(Log architectural or product pivots made based on the feedback log)*
- [ ] TBD
