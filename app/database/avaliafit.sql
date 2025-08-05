-- Tabela: tipo_telefone
CREATE TABLE tipo_telefone (
    id SERIAL PRIMARY KEY,
    nome TEXT,
    user_id INTEGER
);

-- Tabela: tipo_pessoa
CREATE TABLE tipo_pessoa (
    id SERIAL PRIMARY KEY,
    nome TEXT
);

-- Tabela: tipo_endereco
CREATE TABLE tipo_endereco (
    id SERIAL PRIMARY KEY,
    nome TEXT,
    user_id INTEGER
);

-- Tabela: tipo_email
CREATE TABLE tipo_email (
    id SERIAL PRIMARY KEY,
    nome TEXT,
    user_id INTEGER
);

-- Tabela: tipo_cadastro
CREATE TABLE tipo_cadastro (
    id SERIAL PRIMARY KEY,
    nome TEXT,
    user_id INTEGER
);

-- Tabela: estado
CREATE TABLE estado (
    id SERIAL PRIMARY KEY,
    nome TEXT,
    sigla CHAR(2),
    codigo_ibge INTEGER
);

-- Tabela: cidade
CREATE TABLE cidade (
    id SERIAL PRIMARY KEY,
    nome TEXT,
    user_id INTEGER,
    estado_id INTEGER REFERENCES estado(id),
    cep TEXT
);

-- Tabela: pessoa
CREATE TABLE pessoa (
    id SERIAL PRIMARY KEY,
    nome TEXT,
    data_nascimento DATE,
    altura NUMERIC,
    cpf TEXT,
    cnpj TEXT,
    tipo_cadastro_id INTEGER REFERENCES tipo_cadastro(id),
    tipo_pessoa_id INTEGER REFERENCES tipo_pessoa(id),
    user_id INTEGER,
    bloqueado INTEGER
);

-- Tabela: telefone
CREATE TABLE telefone (
    id SERIAL PRIMARY KEY,
    pessoa_id INTEGER REFERENCES pessoa(id),
    user_id INTEGER,
    tipo_telefone_id INTEGER REFERENCES tipo_telefone(id),
    numero TEXT,
    principal INTEGER
);

-- Tabela: endereco
CREATE TABLE endereco (
    id SERIAL PRIMARY KEY,
    tipo_endereco_id INTEGER REFERENCES tipo_endereco(id),
    cidade_id INTEGER REFERENCES cidade(id),
    pessoa_id INTEGER REFERENCES pessoa(id),
    rua TEXT,
    bairro TEXT,
    numero TEXT,
    complemento TEXT,
    user_id INTEGER
);

-- Tabela: email
CREATE TABLE email (
    id SERIAL PRIMARY KEY,
    pessoa_id INTEGER REFERENCES pessoa(id),
    user_id INTEGER,
    tipo_email_id INTEGER REFERENCES tipo_email(id),
    email TEXT,
    principal INTEGER
);


INSERT INTO estado (nome, sigla, codigo_ibge) VALUES
('Acre', 'AC', 12),
('Alagoas', 'AL', 27),
('Amapá', 'AP', 16),
('Amazonas', 'AM', 13),
('Bahia', 'BA', 29),
('Ceará', 'CE', 23),
('Distrito Federal', 'DF', 53),
('Espírito Santo', 'ES', 32),
('Goiás', 'GO', 52),
('Maranhão', 'MA', 21),
('Mato Grosso', 'MT', 51),
('Mato Grosso do Sul', 'MS', 50),
('Minas Gerais', 'MG', 31),
('Pará', 'PA', 15),
('Paraíba', 'PB', 25),
('Paraná', 'PR', 41),
('Pernambuco', 'PE', 26),
('Piauí', 'PI', 22),
('Rio de Janeiro', 'RJ', 33),
('Rio Grande do Norte', 'RN', 24),
('Rio Grande do Sul', 'RS', 43),
('Rondônia', 'RO', 11),
('Roraima', 'RR', 14),
('Santa Catarina', 'SC', 42),
('São Paulo', 'SP', 35),
('Sergipe', 'SE', 28),
('Tocantins', 'TO', 17);


INSERT INTO tipo_pessoa (nome) VALUES
('Física'),
('Jurídica');
