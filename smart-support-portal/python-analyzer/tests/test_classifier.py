# python-analyzer/tests/test_classifier.py
from app.classifier import classify

def test_high_priority():
    p, c, conf = classify("Server crash", "The server is down and throws an error")
    assert p == "high"
    assert c == "technical"
    assert conf == 0.9

def test_medium_priority():
    p, c, conf = classify("Slow API", "Responses are slow with high latency")
    assert p == "medium"
    assert c == "technical"
    assert conf == 0.7

def test_billing_category():
    p, c, conf = classify("Failed payment", "I need a refund for my invoice")
    assert c == "billing"
    assert p in ("high", "low", "medium")

def test_account_category():
    p, c, conf = classify("Password reset", "I cannot login to my account")
    assert c == "account"
