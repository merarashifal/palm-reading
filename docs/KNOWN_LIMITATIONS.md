# Known Limitations (v1.0.0-beta1)

This document tracks known issues and functional boundaries during the beta testing phase. Do not treat these as bugs. They are conscious decisions or pending optimizations.

## Core Engine & Analysis
- **Language Support**: English reports are currently more structurally sound and comprehensive than Hindi.
- **Image Quality**: Poor image quality significantly reduces feature detection confidence.
- **Hand Priority**: Only the dominant palm is supported for analysis. Secondary hand comparisons are not yet implemented.
- **Rare Signs**: Dictionaries for very rare signs (e.g., Mystic Cross, Star on Jupiter) are still under development and may not trigger consistently.

## WordPress & Frontend
- **Device Support**: Mobile (390px) is fully optimized. Tablet viewports may experience minor layout inconsistencies in the report cards.
- **Browser Compatibility**: Safari requires additional QA regarding CSS blur effects and progressive animations.

## Analytics & Tracking
- **Cross-Device Tracking**: Anonymous session state relies on cookies. If a user uploads on desktop and unlocks on mobile, the sessions are not currently merged.
