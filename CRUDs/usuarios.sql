USE gti_bd;

SELECT 
    id_usuario,
    nome_completo,
    email,
    cpf,
    telefone,
    tipo_usuario,
    data_cadastro,
    termos_aceitos
FROM usuario
ORDER BY data_cadastro DESC;