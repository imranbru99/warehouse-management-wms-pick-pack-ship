# Performance

## Drutogoti

`imrandevbd/drutogoti` is installed and configured via `config/drutogoti.php`.

Recommended local settings:

```env
DRUTOGOTI_ENABLED=true
DRUTOGOTI_STORAGE_DRIVER=sync
DRUTOGOTI_SAMPLE_RATE=1.0
DRUTOGOTI_SLOW_THRESHOLD_MS=100
```

Production: sample at `0.1`, storage `queue`, Redis queue connection.

Platform UI: `/platform` → Performance page shows enablement, request counts, and suggestion counts.

## Benchmark note (foundation)

Baseline after Phase 1 (SQLite/MySQL, empty demo):

| Check | Target | Result |
|-------|--------|--------|
| `GET /up` | < 50ms | health endpoint |
| Module catalog cache hit | < 5ms | Redis/array cache |
| Tenant module toggle check | < 2ms cached | `ModuleRegistry::enabled` |

Full ledger/SKU load tests land in Phase 14 (`docs/PERFORMANCE.md` updated with script output).
