<?php
// Script de recebimento direto e envio por e-mail para HostGator (meuaniver.com.br)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);
if (!$input) {
    $input = $_POST;
}

$nome = isset($input['nome']) ? strip_tags(trim($input['nome'])) : '';
$presenca = isset($input['presenca']) ? strip_tags(trim($input['presenca'])) : 'Não informado';
$acompanhante = isset($input['acompanhante']) ? strip_tags(trim($input['acompanhante'])) : 'Nenhum';
$telefone = isset($input['telefone']) ? strip_tags(trim($input['telefone'])) : '';
$mensagem = isset($input['mensagem']) ? strip_tags(trim($input['mensagem'])) : 'Sem mensagem adicional';

if (empty($nome)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Nome obrigatório']);
    exit();
}

$to = 'liege_i@yahoo.com';
$subject = "=?UTF-8?B?" . base64_encode("Confirmação de Presença 50+50: " . $nome) . "?=";

$body = "=====================================================\n";
$body .= "  NOVA CONFIRMAÇÃO DE PRESENÇA - FESTA 50+50 ANOS\n";
$body .= "  Liege & Rogério | Elettra Bar - 14/11\n";
$body .= "=====================================================\n\n";
$body .= "• Convidado: " . $nome . "\n";
$body .= "• Presença: " . $presenca . "\n";
$body .= "• Acompanhante: " . ($acompanhante ?: 'Nenhum') . "\n";
$body .= "• WhatsApp: " . $telefone . "\n";
$body .= "• Mensagem: " . ($mensagem ?: 'Sem mensagem adicional') . "\n\n";
$body .= "-----------------------------------------------------\n";
$body .= "Enviado em: " . date('d/m/Y H:i:s') . "\n";
$body .= "IP do convidado: " . (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'desconhecido') . "\n";

$headers = "From: RSVP 50+50 <contato@meuaniver.com.br>\r\n";
$headers .= "Reply-To: contato@meuaniver.com.br\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

$mailSent = @mail($to, $subject, $body, $headers);

echo json_encode([
    'success' => true,
    'mail_sent' => $mailSent,
    'message' => 'Confirmação registrada com sucesso'
]);
