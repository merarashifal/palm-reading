# Launch Checklist

This checklist must be fully verified before opening the beta to public external users. It tracks operational, legal, and security readiness, not feature implementation.

## Infrastructure
- [ ] Storage directory is writable and isolated.
- [ ] Cron jobs (if applicable for background analytics/cleanup) are running.
- [ ] HTTPS is fully enforced across all paths.
- [ ] Cache layers (Redis/Memcached/Varnish) respect `ppb_visitor_id` and do not cache personalized `/report/` endpoints incorrectly.

## Security
- [ ] Uploads: MIME type validation enforced.
- [ ] Uploads: Maximum file size checked (e.g., 5MB).
- [ ] Uploads: Malicious file execution prevented in storage dir (block PHP execution).
- [ ] AJAX: Nonces successfully verified on all mutation endpoints.
- [ ] Database: Prepared statements used for all SQL.
- [ ] Config: No hard-coded API keys in repository.

## Product Journey
- [ ] Landing page UI scales correctly on mobile (390px).
- [ ] Upload process works smoothly on mobile connections.
- [ ] Processing UI conveys progress and completes without hanging.
- [ ] Free Report renders perfectly.
- [ ] Modal unlock perfectly saves data and hydrates the premium view.
- [ ] Error states (Poor image, timeout, etc.) correctly display user-friendly messages instead of raw exceptions.

## Legal & Compliance
- [ ] Privacy Policy published and linked.
- [ ] Terms of Use published and linked.
- [ ] Disclaimer (e.g., "For entertainment purposes") visible.
- [ ] Explicit consent checkbox verified as functional on profile modal.
- [ ] Data Retention Policy documented and understood by team.

## Analytics & Telemetry
- [ ] Events (`profile_completed`, `insight_expanded`, etc.) successfully logging.
- [ ] Founder Dashboard populates with accurate data.
- [ ] Funnel conversion matches expected pathways.

## Support
- [ ] Contact email / support loop established.
- [ ] Feedback module successfully saving to database.
- [ ] PHP Error logging enabled and monitored by the team.

**Beta Readiness Goal: 100% of these must be checked before public beta.**
