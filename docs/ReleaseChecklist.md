# Release SOP (Standard Operating Procedure)

This checklist must be strictly followed before any major version release of the AI Analysis Engine to guarantee architectural purity, idempotency, and deployment readiness.

## Release Checklist

- [ ] **Architecture Frozen**: No structural refactoring or generic foundation changes are included in this release without an accompanying ADR.
- [ ] **Tests Pass**: Run `vendor/bin/phpunit`. All Unit and Integration tests must pass with 100% success.
- [ ] **Benchmarks Pass**: Run the benchmark suite against `Small`, `Medium`, `Large`, and `Massive` datasets. Ensure O(1) constraints and memory limits are respected.
- [ ] **Coverage Acceptable**: Ensure any newly added pipeline stages are fully covered by idempotency and integration tests.
- [ ] **Knowledge Pack Generated**: Run `engine generate` and verify `generated/latest/` contains all expected Build Package artifacts (`knowledge_pack.json`, `install.sql`, `knowledge_pack.csv`).
- [ ] **Checksums Generated**: Verify `build.json`, `checksums.json`, and the `master_checksum` are valid and present.
- [ ] **CLI Tested**: Verify all Engine CLI commands (`engine doctor`, `engine validate`, `engine compile`, `engine generate`, `engine benchmark`) execute perfectly.
- [ ] **Documentation Updated**: Ensure `ROADMAP.md` is updated and any new ADRs are written.
- [ ] **VERSION Updated**: Ensure `engine/VERSION` reflects the new release tag.
- [ ] **CHANGELOG Updated**: Describe the exact capabilities and pipeline additions in the `CHANGELOG.md`.
- [ ] **Git Tag Created**: Create a strict git tag matching the version string (e.g., `v0.7.0`) and push to the repository.
