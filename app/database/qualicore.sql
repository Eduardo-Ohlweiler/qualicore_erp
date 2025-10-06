-- ========================
-- Tabelas auxiliares
-- ========================

CREATE TABLE tipo_telefone (
    id SERIAL PRIMARY KEY,
    nome TEXT
);

CREATE TABLE tipo_pessoa (
    id SERIAL PRIMARY KEY,
    nome TEXT
);

CREATE TABLE tipo_endereco (
    id SERIAL PRIMARY KEY,
    nome TEXT
);

CREATE TABLE tipo_email (
    id SERIAL PRIMARY KEY,
    nome TEXT
);

CREATE TABLE tipo_cadastro (
    id SERIAL PRIMARY KEY,
    nome TEXT
);

CREATE TABLE estado (
    id SERIAL PRIMARY KEY,
    nome TEXT,
    sigla CHAR(2),
    codigo_ibge INTEGER
);

CREATE TABLE cidade (
    id SERIAL PRIMARY KEY,
    nome TEXT,
    estado_id INTEGER REFERENCES estado(id),
    cep TEXT
);

-- ========================
-- Pessoa
-- ========================

CREATE TABLE pessoa (
    id SERIAL PRIMARY KEY,
    nome TEXT,
    data_nascimento DATE,
    altura NUMERIC,
    cpf TEXT,
    cnpj TEXT,
    tipo_cadastro_id INTEGER REFERENCES tipo_cadastro(id),
    tipo_pessoa_id INTEGER REFERENCES tipo_pessoa(id),
    bloqueado INTEGER
);

-- ========================
-- Contatos
-- ========================

CREATE TABLE telefone (
    id SERIAL PRIMARY KEY,
    pessoa_id INTEGER REFERENCES pessoa(id),
    tipo_telefone_id INTEGER REFERENCES tipo_telefone(id),
    numero TEXT,
    principal INTEGER
);

CREATE TABLE endereco (
    id SERIAL PRIMARY KEY,
    tipo_endereco_id INTEGER REFERENCES tipo_endereco(id),
    cidade_id INTEGER REFERENCES cidade(id),
    pessoa_id INTEGER REFERENCES pessoa(id),
    rua TEXT,
    bairro TEXT,
    numero TEXT,
    complemento TEXT
);

CREATE TABLE email (
    id SERIAL PRIMARY KEY,
    pessoa_id INTEGER REFERENCES pessoa(id),
    tipo_email_id INTEGER REFERENCES tipo_email(id),
    email TEXT,
    principal INTEGER
);

-- ========================
-- Insumos
-- ========================

CREATE TABLE tipo_insumo (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE insumo (
    id SERIAL PRIMARY KEY,
    descricao VARCHAR(255) NOT NULL,
    codigo VARCHAR(50) UNIQUE NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    alterado_em TIMESTAMP,
    criou_pessoa_id INT,
    alterou_pessoa_id INT,
    tipo_insumo_id INT NOT NULL,
    bloqueado INTEGER,
    
    CONSTRAINT fk_tipo_insumo FOREIGN KEY (tipo_insumo_id) REFERENCES tipo_insumo(id)
);

-- ========================
-- Inserts iniciais
-- ========================

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
