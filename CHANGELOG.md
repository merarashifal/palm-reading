# Changelog

All notable changes to this project will be documented in this file.

## [v1.0.0-RC1] - 2026-07-14

### Added
- Gemini Provider Pipeline with strict JSON parsing and deterministic schema enforcement.
- Core Knowledge Engine capable of executing compiled JSON knowledge packs.
- Initial set of curated rules for Palmistry in `palmistry_v1.json`.
- `EngineFacade::analyze` public contract to abstract execution logic from clients.
- Telemetry system tracking execution times, features detected, and raw metrics.
- WordPress integration allowing users to upload a palm and get a generated HTML report via `[palm_reader]` shortcode.

### Changed
- Shifted from a domain-coupled AI application to a domain-agnostic Knowledge Engine.
- AI is now strictly used as a visual observer rather than a reasoning engine, preventing hallucinations.
- Presentation Engine now generates dynamic UI metrics using a decoupled `ReportModel`.

### Fixed
- Non-deterministic responses and markdown block parsing from Gemini.

### Known Issues
- N/A for RC1.

### Breaking Changes
- Completely removed old domain-coupled code. External integration must now route through `EngineFacade`.
