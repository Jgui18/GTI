USE gti_bd;

INSERT INTO plano (nome, descricao, preco_mensal, beneficios) VALUES
('Plano Básico', 'Plano básico para residências', 99.90, 'Coleta mensal, suporte básico'),
('Plano Empresarial', 'Plano completo para empresas', 299.90, 'Coleta semanal, suporte prioritário, relatórios'),
('Plano Premium', 'Plano premium com todos os benefícios', 499.90, 'Coleta sob demanda, suporte 24/7, relatórios detalhados')
ON DUPLICATE KEY UPDATE nome = nome;

INSERT INTO plano_tipo_cacamba (id_plano, tipo_residuo, tamanho, descricao) VALUES
(1, 'Resíduos Metálicos', '3m³', 'Caçamba pequena ideal para resíduos de metal domésticos'),
(1, 'Resíduos de Papel', '3m³', 'Caçamba pequena ideal para resíduos de papel domésticos'),
(1, 'Resíduos de Plástico', '3m³', 'Caçamba pequena ideal para resíduos de plástico domésticos'),
(1, 'Resíduos de Vidro', '3m³', 'Caçamba pequena ideal para resíduos de vidro domésticos'),
(1, 'Resíduos Orgânicos', '3m³', 'Caçamba pequena ideal para resíduos de orgânicos domésticos'),
(1, 'Resíduos de Madeira', '3m³', 'Caçamba pequena ideal para resíduos de madeira domésticos'),
(2, 'Resíduos Metálicos', '5m³', 'Caçamba média para resíduos de metal de empresas'),
(2, 'Resíduos de Papel', '5m³', 'Caçamba média para resíduos de papel de empresas'),
(2, 'Resíduos de Plástico', '5m³', 'Caçamba média para resíduos de plástico de empresas'),
(2, 'Resíduos de Vidro', '5m³', 'Caçamba média para resíduos de vidro de empresas'),
(2, 'Resíduos Orgânicos', '5m³', 'Caçamba média para resíduos orgânicos de empresas'),
(2, 'Resíduos de Madeira', '5m³', 'Caçamba média para resíduos de madeira de empresas'),
(2, 'Resíduos Radioativos', '5m³', 'Caçamba média para resíduos radioativos de empresas'),
(2, 'Resíduos Contaminados', '5m³', 'Caçamba média para resíduos contaminados de empresas'),
(3, 'Resíduos Metálicos', '7m³', 'Caçamba grande para resíduos de metal'),
(3, 'Resíduos de Papel', '7m³', 'Caçamba grande para residuos de papel'),
(3, 'Resíduos de Plástico', '7m³', 'Caçamba grande para residuos de plástico'),
(3, 'Resíduos de Vidro', '7m³', 'Caçamba grande para residuos de vidro'),
(3, 'Resíduos Orgânicos', '7m³', 'Caçamba grande para residuos Orgânicos'),
(3, 'Resíduos de Madeira', '7m³', 'Caçamba grande para residuos de madeira'),
(3, 'Resíduos de Radioativos', '7m³', 'Caçamba grande para residuos radioativos'),
(3, 'Resíduos Contaminados', '7m³', 'Caçamba grande para residuos Contaminados');

SELECT 
    id_cacamba,
    id_plano,
    tipo_residuo,
    tamanho,
    descricao
FROM plano_tipo_cacamba
ORDER BY id_plano, tipo_residuo;

SELECT 
    ptc.id_cacamba,
    ptc.id_plano,
    p.nome AS nome_plano,
    ptc.tipo_residuo,
    ptc.tamanho,
    ptc.descricao,
    p.preco_mensal
FROM plano_tipo_cacamba ptc
INNER JOIN plano p ON ptc.id_plano = p.id_plano
ORDER BY p.nome, ptc.tipo_residuo;

SELECT 
    tipo_residuo,
    COUNT(*) AS quantidade_cacambas,
    GROUP_CONCAT(DISTINCT tamanho ORDER BY tamanho SEPARATOR ', ') AS tamanhos_disponiveis
FROM plano_tipo_cacamba
GROUP BY tipo_residuo
ORDER BY tipo_residuo;

SELECT 
    p.nome AS nome_plano,
    COUNT(ptc.id_cacamba) AS quantidade_cacambas,
    GROUP_CONCAT(DISTINCT ptc.tipo_residuo ORDER BY ptc.tipo_residuo SEPARATOR ', ') AS tipos_disponiveis
FROM plano p
LEFT JOIN plano_tipo_cacamba ptc ON p.id_plano = ptc.id_plano
GROUP BY p.id_plano, p.nome
ORDER BY p.nome;

SELECT 
    tamanho,
    COUNT(*) AS quantidade,
    GROUP_CONCAT(DISTINCT tipo_residuo ORDER BY tipo_residuo SEPARATOR ', ') AS tipos_disponiveis
FROM plano_tipo_cacamba
GROUP BY tamanho
ORDER BY 
    CASE tamanho
        WHEN '3m³' THEN 1
        WHEN '5m³' THEN 2
        WHEN '7m³' THEN 3
        WHEN '10m³' THEN 4
        WHEN '15m³' THEN 5
        ELSE 6
    END;

SELECT 
    ptc.id_cacamba,
    ptc.tipo_residuo,
    ptc.tamanho,
    ptc.descricao
FROM plano_tipo_cacamba ptc
WHERE ptc.id_plano = 1
ORDER BY ptc.tipo_residuo;

SELECT 
    ptc.id_cacamba,
    p.nome AS nome_plano,
    ptc.tamanho,
    ptc.descricao
FROM plano_tipo_cacamba ptc
INNER JOIN plano p ON ptc.id_plano = p.id_plano
WHERE ptc.tipo_residuo = 'Resíduos Recicláveis'
ORDER BY p.nome, ptc.tamanho;