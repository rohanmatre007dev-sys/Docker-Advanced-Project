# python-analyzer/app/classifier.py
from typing import Tuple
import re

HIGH_KEYWORDS = {'error', 'crash', 'down', 'failed', 'failure'}
MEDIUM_KEYWORDS = {'slow', 'delay', 'latency'}
BILLING_KEYWORDS = {'invoice', 'payment', 'refund', 'billing'}
ACCOUNT_KEYWORDS = {'password', 'login', 'account', 'signup'}
TECH_KEYWORDS = {'server', 'bug', 'api', 'database', 'connection', 'timeout'}

def _contains_any(text: str, keywords: set) -> bool:
    text = text.lower()
    for kw in keywords:
        if re.search(r'\b' + re.escape(kw) + r'\b', text):
            return True
    return False

def classify(title: str, description: str) -> Tuple[str, str, float]:
    txt = f"{title} {description}".lower()

    # Priority
    if _contains_any(txt, HIGH_KEYWORDS):
        priority = "high"
        confidence = 0.9
    elif _contains_any(txt, MEDIUM_KEYWORDS):
        priority = "medium"
        confidence = 0.7
    else:
        priority = "low"
        confidence = 0.5

    # Category
    if _contains_any(txt, BILLING_KEYWORDS):
        category = "billing"
    elif _contains_any(txt, ACCOUNT_KEYWORDS):
        category = "account"
    elif _contains_any(txt, TECH_KEYWORDS):
        category = "technical"
    else:
        category = "other"

    return priority, category, confidence
