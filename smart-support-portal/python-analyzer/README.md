# Python Analyzer - smart-support-portal/python-analyzer

FastAPI service that provides a simple rule-based classifier.

Env vars:

- REDIS_HOST (default: localhost)
- REDIS_PORT (default: 6379)

Run locally:

```bash
pip install -r requirements.txt
uvicorn app.main:app --host 0.0.0.0 --port 8000
```
