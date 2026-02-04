# python-analyzer/app/models.py
from pydantic import BaseModel, Field
from typing import Literal

Priority = Literal["low", "medium", "high"]
Category = Literal["billing", "technical", "account", "other"]

class TicketRequest(BaseModel):
    ticket_id: int
    title: str
    description: str

class AnalysisResult(BaseModel):
    priority: Priority
    category: Category
    confidence: float = Field(..., ge=0.0, le=1.0)
