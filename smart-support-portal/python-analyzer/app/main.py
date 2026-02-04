# python-analyzer/app/main.py
from fastapi import FastAPI, HTTPException
import os
from .models import TicketRequest, AnalysisResult
from .classifier import classify
from .redis_cache import get_cached_analysis, set_cached_analysis
import redis

app = FastAPI()

REDIS_HOST = os.getenv("REDIS_HOST", "localhost")
REDIS_PORT = int(os.getenv("REDIS_PORT", "6379"))

@app.on_event("startup")
def startup_event():
    app.state.redis = redis.Redis(host=REDIS_HOST, port=REDIS_PORT, decode_responses=True)

@app.get("/health")
def health():
    return {"status": "ok"}

@app.post("/analyze", response_model=AnalysisResult)
def analyze(req: TicketRequest):
    r = app.state.redis
    cached = get_cached_analysis(r, req.ticket_id)
    if cached:
        return cached

    priority, category, confidence = classify(req.title, req.description)
    result = AnalysisResult(priority=priority, category=category, confidence=confidence)
    set_cached_analysis(r, req.ticket_id, result)
    return result
