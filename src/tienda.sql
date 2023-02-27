DROP TABLE IF EXISTS articulos CASCADE;
CREATE TABLE articulos (
    id              bigserial       PRIMARY KEY,
    codigo          varchar(13)     NOT NULL UNIQUE,
    descripcion     varchar(255)    NOT NULL,
    precio          numeric(7, 2)   NOT NULL,
    categoria_id    bigint          NOT NULL REFERENCES categorias(id),
    stock           int             NOT NULL --stock en artículos.
);

DROP TABLE IF EXISTS categorias CASCADE;
CREATE TABLE categorias (
  id            bigserial    PRIMARY KEY,
  categoria     varchar(255) NOT NULL
);

DROP TABLE IF EXISTS usuarios CASCADE;
CREATE TABLE usuarios (
    id          bigserial    PRIMARY KEY,
    usuario     varchar(255) NOT NULL UNIQUE,
    nombre      varchar(255),
    apellidos   varchar(255),
    email       varchar(255),
    telefono    varchar(9),
    password    varchar(255) NOT NULL,
    validado    bool         NOT NULL
);

DROP TABLE IF EXISTS facturas CASCADE;
CREATE TABLE facturas (
    id         bigserial  PRIMARY KEY,
    created_at timestamp  NOT NULL DEFAULT localtimestamp(0),
    usuario_id bigint NOT NULL REFERENCES usuarios (id)
);

DROP TABLE IF EXISTS articulos_facturas CASCADE;
CREATE TABLE articulos_facturas (
    articulo_id bigint NOT NULL REFERENCES articulos (id) ON DELETE CASCADE,
    factura_id  bigint NOT NULL REFERENCES facturas (id),
    cantidad    int    NOT NULL,
    PRIMARY KEY (articulo_id, factura_id)
);

-- Carga inicial de datos de prueba:
INSERT INTO articulos (codigo, descripcion, precio, categoria_id, stock)
    VALUES ('18273892389', 'Yogur piña', 200.50, 2, 4),
           ('83745828273', 'Tigretón', 50.10, 2, 23),
           ('51736128495', 'Disco duro SSD 500 GB', 150.30, 1, 0),
           ('83746828273', 'Doritos', 50.10, 2, 30),
           ('51786128435', 'Pantalla HP HD', 150.30, 1, 50),
           ('83745228673', 'Magdalenas', 50.10, 2, 8),
           ('51786198495', 'Pelota de playa', 150.30, 3, 1);

INSERT INTO categorias (categoria) 
VALUES ('Informática'),
       ('Alimentación'),
       ('Otros');

INSERT INTO usuarios (usuario, password, validado)
    VALUES ('admin', crypt('admin', gen_salt('bf', 10)), true),
           ('pepe', crypt('pepe', gen_salt('bf', 10)), false);