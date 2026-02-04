# API Documentation - smart-support-portal/docs/API.md

## PHP App Routes (public pages)

- GET /login
  - Show login form.
- POST /login
  - Authenticate user (email, password).
- GET /register
  - Show registration form.
- POST /register
  - Register new user (email, password, password_confirm).
- GET /logout
  - Clear session and redirect to login.
- GET /tickets
  - Show list of tickets for logged-in user.
- GET /tickets/new
  - Show form to create a ticket.
- POST /tickets
  - Create new ticket (title, description).
  - After creating, PHP calls the analyzer service at /analyze to fill priority/category/confidence.

## Python Analyzer (FastAPI)

- GET /health
  - Response:
    ```json
    { "status": "ok" }
    ```

- POST /analyze
  - Request body (JSON):
    ```json
    {
      "ticket_id": 123,
      "title": "Cannot login",
      "description": "I cannot login, gets error 500"
    }
    ```
  - Response body (JSON):
    ```json
    {
      "priority": "high",
      "category": "account",
      "confidence": 0.9
    }
    ```
  - Behavior:
    - The analyzer first attempts to read a cached result from Redis using key `analysis:<ticket_id>`.
    - If not cached, it runs a simple rule-based classifier and stores the result in Redis for future requests.

## How PHP calls the analyzer

- After creating a ticket, the PHP `TicketController::create` sends a POST JSON payload to:
  `http://{ANALYZER_HOST}:{ANALYZER_PORT}/analyze`
- The PHP app reads `ANALYZER_HOST` and `ANALYZER_PORT` from environment variables.
- On success, PHP updates the `tickets` row with priority, category, and confidence returned by the analyzer.
