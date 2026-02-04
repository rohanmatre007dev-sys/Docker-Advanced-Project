# python-analyzer/app/redis_cache.py
from typing import Optional
import json
from .models import AnalysisResult

KEY_PREFIX = "analysis:"

def get_cached_analysis(redis_client, ticket_id: int) -> Optional[AnalysisResult]:
    key = KEY_PREFIX + str(ticket_id)
    val = redis_client.get(key)
    if not val:
        return None
    try:
        data = json.loads(val)
        return AnalysisResult(**data)
    except Exception:
        return None

def set_cached_analysis(redis_client, ticket_id: int, result: AnalysisResult, ttl: int = 3600):
    key = KEY_PREFIX + str(ticket_id)
    redis_client.set(key, result.json(), ex=ttl)
