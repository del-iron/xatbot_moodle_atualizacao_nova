<?php
header("Content-Type: text/plain");
session_start();

$message = strtolower(trim($_POST["message"] ?? ""));

// Verifica se o chat acabou de ser aberto
if (!isset($_SESSION["chat_started"])) {
    $_SESSION["chat_started"] = true;
    
    // Atraso proposital para simular comportamento humano
    usleep(1000000); // 1 segundo
    
    echo "Olá, eu sou Toinha! Como posso te ajudar hoje? Antes de começarmos, qual é o seu nome?";
    $_SESSION["waiting_for_name"] = true;
    exit;
}

// Verifica se está esperando o nome do usuário
if (isset($_SESSION["waiting_for_name"])) {
    $_SESSION["user_name"] = ucfirst($message);
    unset($_SESSION["waiting_for_name"]);
    
    // Atraso proposital para simular comportamento humano
    usleep(1500000); // 1.5 segundos
    
    echo "Prazer em te conhecer, {$_SESSION["user_name"]}! Como posso te ajudar?";
    exit;
}

$user_name = $_SESSION["user_name"] ?? "Usuário";

// Dicionário de respostas com palavras-chave
$respostas_keywords = [
    implode(",", ["senha", "recuperar", "esqueci", "alterar", "mudar", "trocar"]) => "Você pode recuperar ou alterar sua senha clicando em 'Esqueci minha senha' na página de login do Moodle e seguindo as instruções enviadas para seu e-mail. Te ajudo com mais alguma coisa?",
    implode(",", ["acessar", "entrar", "login", "logar"]) => "Para acessar o Moodle, acesse o site da sua instituição e faça login com seu usuário e senha. Caso tenha esquecido sua senha, use a opção 'Esqueci minha senha'. Te ajudo com mais alguma coisa?",
    implode(",", ["moodle", "plataforma", "sistema"]) => "O Moodle é uma plataforma de ensino à distância usada para cursos online. Ele permite acesso a materiais, atividades e avaliações de forma digital. Te ajudo com mais alguma coisa?",
    implode(",", ["atividade", "enviar", "submeter", "trabalho", "tarefa"]) => "No Moodle, vá até a disciplina desejada, encontre a atividade, clique nela e siga as instruções para enviar seu arquivo ou resposta. Te ajudo com mais alguma coisa? ",
    implode(",", ["professores", "professor", "ver", "visualizar", "atividades"]) => "Sim! Seus professores podem visualizar suas atividades enviadas e fornecer feedback e notas através do Moodle. Te ajudo com mais alguma coisa?",
    implode(",", ["contato", "contatar", "professor", "professores", "mensagem"]) => "Você pode entrar em contato pelo fórum da disciplina, pelo sistema de mensagens do Moodle ou pelo e-mail institucional, se disponível. Te ajudo com mais alguma coisa? ",
    implode(",", ["problema", "erro", "não enviada", "falha"]) => "Se houver problemas no envio, tente novamente. Se o erro persistir, entre em contato com seu professor ou suporte técnico do Moodle da sua instituição. Te ajudo com mais alguma coisa? ",
    implode(",", ["aplicativo", "app", "celular", "mobile", "smartphone"]) => "Sim! O Moodle possui um aplicativo oficial disponível para Android e iOS. Você pode baixá-lo na Play Store ou App Store e acessar seus cursos pelo celular. Te ajudo com mais alguma coisa? ",
    implode(",", ["notas", "nota", "avaliação", "pontuação", "desempenho"]) => "No Moodle, acesse a disciplina e clique na opção 'Notas' para visualizar seu desempenho nas atividades e avaliações realizadas. Te ajudo com mais alguma coisa? ",
    implode(",", ["nota errada", "erro nota", "corrigir nota", "problema nota"]) => "Se houver algum erro em sua nota, entre em contato com seu professor para verificar o problema e solicitar uma correção, se necessário. Te ajudo com mais alguma coisa? "
];

// Buscar resposta baseada em palavras-chave
$resposta = null;
foreach ($respostas_keywords as $keywords => $resp) {
    $keywords_array = explode(",", $keywords);  // Convertendo a chave de volta para um array
    foreach ($keywords_array as $keyword) {
        if (strpos($message, $keyword) !== false) {
            $resposta = $resp;
            break 2;
        }
    }
}

// Se nenhuma palavra-chave foi encontrada, usar resposta padrão
if ($resposta === null) {
    if (!isset($_SESSION['erro_count'])) {
        $_SESSION['erro_count'] = 0;
    }
    
    $_SESSION['erro_count']++;

    if ($_SESSION['erro_count'] == 1) {
        $resposta = "$user_name, desculpe, reformule sua pergunta, por favor!";
    } elseif ($_SESSION['erro_count'] == 2) {
        $resposta = "$user_name, não estou entendendo, por gentileza, reformular sua pergunta!";
    } else {
        $resposta = "$user_name, sinto muito, não consegui te entender. Encerrando o chat!";
        session_unset();
        session_destroy();
    }
}

// Atraso proposital para simular comportamento humano
usleep(rand(2000000, 4000000)); // Entre 2 e 4 segundos

echo $resposta;
?>