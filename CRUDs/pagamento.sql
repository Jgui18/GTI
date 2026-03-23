USE gti_bd;

SELECT 
    u.id_usuario,
    u.email AS login_usuario,
    u.nome_completo AS nome_usuario,
    p.id_pagamento,
    p.plano,
    CASE 
        WHEN p.plano = 'premium' THEN 'Premium'
        WHEN p.plano = 'basico' THEN 'Básico'
        ELSE p.plano
    END AS plano_formatado,
    p.metodo_pagamento,
    CASE 
        WHEN p.metodo_pagamento = 'credit' THEN 'Cartão de Crédito'
        WHEN p.metodo_pagamento = 'pix' THEN 'PIX'
        WHEN p.metodo_pagamento = 'boleto' THEN 'Boleto'
        ELSE p.metodo_pagamento
    END AS metodo_formatado,
    p.nome_completo AS nome_pagamento,
    p.cpf,
    p.email AS email_pagamento,
    p.telefone,
    p.data_nascimento,
    p.valor,
    p.parcelamento,
    CASE 
        WHEN p.parcelamento > 1 THEN CONCAT(p.parcelamento, 'x de R$', FORMAT(p.valor / p.parcelamento, 2, 'de_DE'))
        ELSE 'À vista'
    END AS forma_pagamento,
    p.status_pagamento,
    CASE 
        WHEN p.status_pagamento = 'pendente' THEN 'Pendente'
        WHEN p.status_pagamento = 'aprovado' THEN 'Aprovado'
        WHEN p.status_pagamento = 'cancelado' THEN 'Cancelado'
        WHEN p.status_pagamento = 'reembolsado' THEN 'Reembolsado'
        ELSE p.status_pagamento
    END AS status_formatado,
    p.data_pagamento,
    p.data_atualizacao
FROM pagamentos p
INNER JOIN usuario u ON p.id_usuario = u.id_usuario
ORDER BY p.data_pagamento DESC;

SELECT 
    u.email AS login_usuario,
    u.nome_completo AS nome_usuario,
    p.plano,
    p.nome_completo AS nome_pagamento,
    p.cpf,
    p.email AS email_pagamento,
    p.telefone,
    p.valor,
    p.metodo_pagamento,
    p.status_pagamento,
    DATE_FORMAT(p.data_pagamento, '%d/%m/%Y %H:%i:%s') AS data_pagamento_formatada
FROM pagamentos p
INNER JOIN usuario u ON p.id_usuario = u.id_usuario
WHERE p.status_pagamento = 'aprovado'
ORDER BY p.data_pagamento DESC;

SELECT 
    u.email AS login_usuario,
    p.plano,
    p.nome_completo,
    p.cpf,
    p.email AS email_pagamento,
    p.telefone,
    p.valor,
    p.metodo_pagamento,
    p.status_pagamento,
    p.data_pagamento
FROM pagamentos p
INNER JOIN usuario u ON p.id_usuario = u.id_usuario
WHERE u.email = 'email_do_usuario@exemplo.com'
ORDER BY p.data_pagamento DESC;

SELECT 
    u.email AS login_usuario,
    u.nome_completo AS nome_usuario,
    p.plano,
    p.valor,
    p.metodo_pagamento,
    p.status_pagamento,
    p.data_pagamento
FROM pagamentos p
INNER JOIN usuario u ON p.id_usuario = u.id_usuario
WHERE p.plano = 'premium'
ORDER BY p.data_pagamento DESC;