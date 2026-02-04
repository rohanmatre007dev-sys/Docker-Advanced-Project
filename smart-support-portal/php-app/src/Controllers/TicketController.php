<?php
// php-app/src/Controllers/TicketController.php
declare(strict_types=1);

class TicketController
{
    private PDO $pdo;
    private $redis;
    private string $analyzerHost;
    private string $analyzerPort;

    public function __construct(PDO $pdo, $redis = null, string $analyzerHost = 'python-analyzer', string $analyzerPort = '8000')
    {
        $this->pdo = $pdo;
        $this->redis = $redis;
        $this->analyzerHost = $analyzerHost;
        $this->analyzerPort = $analyzerPort;
    }

    private function ensureAuth()
    {
        if (!isset($_SESSION['user_id'])) {
            redirect('/login');
        }
    }

    public function index()
    {
        $this->ensureAuth();
        require_once __DIR__ . '/../Models/Ticket.php';
        $tickets = Ticket::getByUserId($this->pdo, (int)$_SESSION['user_id']);
        view('tickets.php', [
            'title' => 'My Tickets',
            'tickets' => $tickets,
            'message' => flash('message'),
        ]);
    }

    public function createForm()
    {
        $this->ensureAuth();
        view('ticket_create.php', ['title' => 'Create Ticket', 'error' => flash('error')]);
    }

    public function create()
    {
        $this->ensureAuth();
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (!$title || !$description) {
            flash('error', 'Title and description required');
            redirect('/tickets/new');
        }

        require_once __DIR__ . '/../Models/Ticket.php';
        $ticketId = Ticket::create($this->pdo, (int)$_SESSION['user_id'], $title, $description);

        // Call analyzer
        $analyzerUrl = sprintf('http://%s:%s/analyze', $this->analyzerHost, $this->analyzerPort);
        $payload = json_encode([
            'ticket_id' => (int)$ticketId,
            'title' => $title,
            'description' => $description
        ]);

        $ch = curl_init($analyzerUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($response && $httpCode >= 200 && $httpCode < 300) {
            $data = json_decode($response, true);
            if (is_array($data)) {
                $priority = $data['priority'] ?? null;
                $category = $data['category'] ?? null;
                $confidence = isset($data['confidence']) ? (float)$data['confidence'] : null;
                Ticket::updateAnalysis($this->pdo, (int)$ticketId, $priority, $category, $confidence);
            }
        } else {
            // Logging could be added; for now, ignore analyzer failures.
        }

        flash('message', 'Ticket created');
        redirect('/tickets');
    }
}
