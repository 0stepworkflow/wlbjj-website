<?php
// Contact form mailer for washingtonluisbjj.com (Hostinger PHP).
// Replaces FormSubmit. Sends Mike a styled "New submission received" email.

// ---- Settings: change these three lines when launching ----
$TO_EMAIL   = '0stepworkflow@gmail.com';          // TESTING: Cristian's inbox. Switch to mike.wlbjj@gmail.com at launch.
$FROM_EMAIL = 'noreply@washingtonluisbjj.com';    // must be an address on this domain
$SITE_NAME  = 'Washington Luis Brazilian Jiu-Jitsu';
// ------------------------------------------------------------

header('Content-Type: application/json; charset=utf-8');

function fail($code, $msg) {
  http_response_code($code);
  echo json_encode(['ok' => false, 'error' => $msg]);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') fail(405, 'POST only');

// Spam trap: real visitors never fill the hidden "_honey" field.
if (!empty($_POST['_honey'])) { echo json_encode(['ok' => true]); exit; }

function clean($v, $max = 2000) {
  $v = trim((string)$v);
  $v = str_replace(["\r", "\n"], ' ', $v);          // no header injection
  return mb_substr($v, 0, $max);
}

$name    = clean($_POST['name'] ?? '', 120);
$phone   = clean($_POST['phone'] ?? '', 40);
$email   = clean($_POST['email'] ?? '', 160);
$program = clean($_POST['program'] ?? '', 60);
$message = trim((string)($_POST['message'] ?? ''));
$message = mb_substr(str_replace("\r", '', $message), 0, 4000);

if ($name === '' || $phone === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
  fail(422, 'Please fill in your name, phone, and a valid email.');
}

$programs = [
  'adult-fundamentals' => 'Adult Fundamentals',
  'adult'              => 'Adult Gi & No-Gi',
  'striking'           => 'Adult Striking',
  'womens'             => 'Adult Women Only Jiu Jitsu',
  'little-ninjas'      => 'Little Ninjas (Ages 4-6)',
  'juniors'            => 'Juniors Gi & No-Gi (Ages 7-14)',
  'junior-competition' => 'Juniors Competition Team',
  'private'            => 'Private Lessons',
  'not-sure'           => 'Not Sure Yet',
];
$programLabel = $programs[$program] ?? 'Not specified';

function h($s) { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

$rows = [
  'Full Name'     => $name,
  'Phone'         => $phone,
  'Email'         => $email,
  'Interested In' => $programLabel,
  'Message'       => $message === '' ? '(none)' : $message,
];

$html  = '<div style="background:#f2f2f2;padding:24px;font-family:Arial,Helvetica,sans-serif;color:#222;">';
$html .= '<div style="max-width:560px;margin:0 auto;">';
$html .= '<h1 style="font-size:24px;margin:0 0 16px;">New submission received</h1>';
$html .= '<p style="margin:0 0 16px;">Someone just filled out the free class form on your website.</p>';
$html .= '<div style="background:#e6e6e6;border-radius:6px;padding:20px;">';
$html .= '<h2 style="font-size:16px;margin:0 0 12px;">Notification Summary</h2>';
foreach ($rows as $label => $value) {
  $html .= '<p style="margin:0 0 10px;"><strong>' . h($label) . ':</strong><br>' . nl2br(h($value)) . '</p>';
}
$html .= '</div>';
$html .= '<p style="font-size:12px;color:#777;margin-top:16px;">Reply to this email to answer them directly. Sent from ' . h($SITE_NAME) . ' website.</p>';
$html .= '</div></div>';

$text = "New submission received\n\n";
foreach ($rows as $label => $value) $text .= "$label: $value\n";

$boundary = md5(uniqid('', true));
$headers  = "From: $SITE_NAME <$FROM_EMAIL>\r\n";
$headers .= "Reply-To: $name <$email>\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: multipart/alternative; boundary=\"$boundary\"\r\n";

$body  = "--$boundary\r\nContent-Type: text/plain; charset=UTF-8\r\n\r\n$text\r\n";
$body .= "--$boundary\r\nContent-Type: text/html; charset=UTF-8\r\n\r\n$html\r\n";
$body .= "--$boundary--";

$subject = 'New free class request - ' . $name;
$ok = mail($TO_EMAIL, '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, $headers, '-f' . $FROM_EMAIL);

if (!$ok) fail(500, 'Mail could not be sent.');
echo json_encode(['ok' => true]);
