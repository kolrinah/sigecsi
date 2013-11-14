--
-- PostgreSQL database dump
--

-- Dumped from database version 9.0.4
-- Dumped by pg_dump version 9.0.4
-- Started on 2013-07-28 23:17:08

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

SET search_path = public, pg_catalog;

SET default_with_oids = false;

--
-- TOC entry 1608 (class 1259 OID 42853)
-- Dependencies: 1904 5
-- Name: p_actividades; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE p_actividades (
    id_actividad integer NOT NULL,
    iov_act text NOT NULL,
    mv_act text NOT NULL,
    um_act text NOT NULL,
    supuestos_act text NOT NULL,
    codigo_act character varying(10) NOT NULL,
    id_accion integer NOT NULL,
    id_responsable integer NOT NULL,
    descripcion_act text NOT NULL,
    fecha_ini date NOT NULL,
    fecha_fin date NOT NULL,
    id_fuente smallint DEFAULT 1 NOT NULL
);


--
-- TOC entry 1607 (class 1259 OID 42851)
-- Dependencies: 5 1608
-- Name: p_actividades_id_actividad_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE p_actividades_id_actividad_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 1927 (class 0 OID 0)
-- Dependencies: 1607
-- Name: p_actividades_id_actividad_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE p_actividades_id_actividad_seq OWNED BY p_actividades.id_actividad;


--
-- TOC entry 1928 (class 0 OID 0)
-- Dependencies: 1607
-- Name: p_actividades_id_actividad_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('p_actividades_id_actividad_seq', 1, true);


--
-- TOC entry 1624 (class 1259 OID 79205)
-- Dependencies: 5
-- Name: p_fuentes; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE p_fuentes (
    id_fuente smallint NOT NULL,
    cod_fuente character varying(4) NOT NULL,
    fuente character varying(30) NOT NULL
);


--
-- TOC entry 1623 (class 1259 OID 79203)
-- Dependencies: 5 1624
-- Name: p_fuentes_id_fuente_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE p_fuentes_id_fuente_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 1929 (class 0 OID 0)
-- Dependencies: 1623
-- Name: p_fuentes_id_fuente_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE p_fuentes_id_fuente_seq OWNED BY p_fuentes.id_fuente;


--
-- TOC entry 1930 (class 0 OID 0)
-- Dependencies: 1623
-- Name: p_fuentes_id_fuente_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('p_fuentes_id_fuente_seq', 3, true);


--
-- TOC entry 1622 (class 1259 OID 71017)
-- Dependencies: 1906 5
-- Name: p_presupuesto_actividad; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE p_presupuesto_actividad (
    id_presupuesto bigint NOT NULL,
    id_actividad bigint NOT NULL,
    id_partida character varying(30) NOT NULL,
    descripcion_gasto text NOT NULL,
    um character varying(20) NOT NULL,
    cantidad numeric DEFAULT 1 NOT NULL,
    costo_unitario numeric NOT NULL
);


--
-- TOC entry 1621 (class 1259 OID 71015)
-- Dependencies: 5 1622
-- Name: p_presupuesto_actividad_id_presupuesto_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE p_presupuesto_actividad_id_presupuesto_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 1931 (class 0 OID 0)
-- Dependencies: 1621
-- Name: p_presupuesto_actividad_id_presupuesto_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE p_presupuesto_actividad_id_presupuesto_seq OWNED BY p_presupuesto_actividad.id_presupuesto;


--
-- TOC entry 1932 (class 0 OID 0)
-- Dependencies: 1621
-- Name: p_presupuesto_actividad_id_presupuesto_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('p_presupuesto_actividad_id_presupuesto_seq', 1, true);


--
-- TOC entry 1620 (class 1259 OID 70995)
-- Dependencies: 5
-- Name: z_partidas_presupuestarias; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE z_partidas_presupuestarias (
    id_partida character varying(30) NOT NULL,
    denominacion text NOT NULL,
    id_sup character varying(30) NOT NULL
);


--
-- TOC entry 1903 (class 2604 OID 42856)
-- Dependencies: 1607 1608 1608
-- Name: id_actividad; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE p_actividades ALTER COLUMN id_actividad SET DEFAULT nextval('p_actividades_id_actividad_seq'::regclass);


--
-- TOC entry 1907 (class 2604 OID 79211)
-- Dependencies: 1624 1623 1624
-- Name: id_fuente; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE p_fuentes ALTER COLUMN id_fuente SET DEFAULT nextval('p_fuentes_id_fuente_seq'::regclass);


--
-- TOC entry 1905 (class 2604 OID 71020)
-- Dependencies: 1621 1622 1622
-- Name: id_presupuesto; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE p_presupuesto_actividad ALTER COLUMN id_presupuesto SET DEFAULT nextval('p_presupuesto_actividad_id_presupuesto_seq'::regclass);


--
-- TOC entry 1921 (class 0 OID 42853)
-- Dependencies: 1608
-- Data for Name: p_actividades; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 1924 (class 0 OID 79205)
-- Dependencies: 1624
-- Data for Name: p_fuentes; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO p_fuentes VALUES (1, '01', 'Ingresos Ordinarios');
INSERT INTO p_fuentes VALUES (2, '07', 'Otros Ingresos');
INSERT INTO p_fuentes VALUES (3, '08', 'Gestión Fiscal');


--
-- TOC entry 1923 (class 0 OID 71017)
-- Dependencies: 1622
-- Data for Name: p_presupuesto_actividad; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 1922 (class 0 OID 70995)
-- Dependencies: 1620
-- Data for Name: z_partidas_presupuestarias; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO z_partidas_presupuestarias VALUES ('4.02.06.03.00', 'Tintas, pinturas y colorantes', '4.02.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.06.04.00', 'Productos farmacéuticos y medicamentos', '4.02.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.06.05.00', 'Productos de tocador', '4.02.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.06.06.00', 'Combustibles y lubricantes', '4.02.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.06.07.00', 'Productos diversos derivados del petróleo y del carbón', '4.02.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.06.08.00', 'Productos plásticos', '4.02.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.06.09.00', 'Mezclas explosivas', '4.02.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.06.99.00', 'Otros productos de la industria química y conexos', '4.02.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.07.00.00', 'Productos minerales no metálicos', '4.02.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.07.01.00', 'Productos de barro, loza y porcelana', '4.02.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.07.02.00', 'Vidrios y productos de vidrio', '4.02.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.07.03.00', 'Productos de arcilla para construcción', '4.02.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.07.04.00', 'Cemento, cal y yeso', '4.02.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.07.99.00', 'Otros productos minerales no metálicos', '4.02.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.08.00.00', 'Productos metálicos', '4.02.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.08.01.00', 'Productos primarios de hierro y acero', '4.02.08.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.08.02.00', 'Productos de metales no ferrosos', '4.02.08.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.08.03.00', 'Herramientas menores, cuchillería y artículos generales de ferretería', '4.02.08.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.08.04.00', 'Productos metálicos estructurales', '4.02.08.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.08.05.00', 'Materiales de  orden público, seguridad y defensa ', '4.02.08.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.08.07.00', 'Material de señalamiento', '4.02.08.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.08.08.00', 'Material de educación', '4.02.08.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.08.09.00', 'Repuestos y accesorios para equipos de transporte', '4.02.08.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.08.10.00', 'Repuestos y accesorios para otros equipos', '4.02.08.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.08.99.00', 'Otros productos metálicos', '4.02.08.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.09.00.00', 'Productos de madera', '4.02.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.09.01.00', 'Productos primarios de madera', '4.02.09.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.09.02.00', 'Muebles y accesorios de madera para edificaciones', '4.02.09.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.09.99.00', 'Otros productos de madera', '4.02.09.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.10.00.00', 'Productos varios y útiles diversos', '4.02.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.10.01.00', 'Artículos de deporte, recreación y juguetes', '4.02.10.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.10.02.00', 'Materiales y útiles de limpieza y aseo', '4.02.10.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.10.03.00', 'Utensilios de cocina y comedor', '4.02.10.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.10.04.00', 'Útiles menores médico-quirúrgicos de laboratorio, dentales y de veterinaria', '4.02.10.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.10.05.00', 'Útiles de escritorio, oficina y materiales de instrucción', '4.02.10.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.10.06.00', 'Condecoraciones, ofrendas y similares', '4.02.10.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.10.07.00', 'Productos de seguridad en el trabajo', '4.02.10.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.10.08.00', 'Materiales para equipos de computación', '4.02.10.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.10.09.00', 'Especies timbradas y valores', '4.02.10.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.10.10.00', 'Útiles religiosos', '4.02.10.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.10.11.00', 'Materiales eléctricos', '4.02.10.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.10.12.00', 'Materiales para instalaciones sanitarias', '4.02.10.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.10.13.00', 'Materiales fotográficos', '4.02.10.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.10.99.00', 'Otros productos y útiles diversos', '4.02.10.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.11.00.00', 'Bienes para la venta', '4.02.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.11.01.00', 'Productos y artículos para la venta', '4.02.11.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.11.02.00', 'Maquinaria y equipos para la venta', '4.02.11.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.11.03.00', 'Inmuebles para la venta', '4.02.11.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.11.04.00', 'Tierras y terrenos para la venta', '4.02.11.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.11.99.00', 'Otros bienes para la venta    ', '4.02.11.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.99.00.00', 'Otros materiales y suministros', '4.02.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.99.01.00', 'Otros materiales y suministros', '4.02.99.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.00.00.00', 'SERVICIOS NO PERSONALES', '4.00.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.01.00.00', 'Alquileres de inmuebles', '4.03.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.01.01.00', 'Alquileres de edificios y locales', '4.03.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.01.02.00', 'Alquileres de instalaciones culturales y recreativas', '4.03.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.01.03.00', 'Alquileres de tierras y terrenos ', '4.03.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.02.00.00', 'Alquileres de maquinaria y equipos', '4.03.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.02.01.00', 'Alquileres de maquinaria y demás equipos de construcción, campo, industria y taller', '4.03.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.02.02.00', 'Alquileres de equipos de transporte, tracción y elevación', '4.03.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.02.03.00', 'Alquileres de equipos de comunicaciones y de señalamiento', '4.03.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.02.04.00', 'Alquileres de equipos médico-quirúrgicos, dentales y de veterinaria', '4.03.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.02.05.00', 'Alquileres de equipos científicos, religiosos,  de enseñanza y recreación', '4.03.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.02.06.00', 'Alquileres de máquinas, muebles y demás equipos de oficina y alojamiento', '4.03.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.02.99.00', 'Alquileres de otras maquinaria y equipos', '4.03.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.03.00.00', 'Derechos sobre bienes intangibles', '4.03.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.03.01.00', 'Marcas de fábrica y patentes de invención', '4.03.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.03.02.00', 'Derechos de autor', '4.03.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.03.03.00', 'Paquetes y programas de computación', '4.03.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.03.04.00', 'Concesión de bienes y servicios', '4.03.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.04.00.00', 'Servicios básicos', '4.03.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.04.01.00', 'Electricidad', '4.03.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.04.02.00', 'Gas', '4.03.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.04.03.00', 'Agua', '4.03.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.04.04.00', 'Teléfonos', '4.03.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.04.05.00', 'Servicio de comunicaciones', '4.03.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.04.06.00', 'Servicio de aseo urbano y domiciliario', '4.03.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.04.07.00', 'Servicio de condominio', '4.03.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.05.00.00', 'Servicio de administración, vigilancia y mantenimiento de los servicios básicos', '4.03.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.05.01.00', 'Servicio de administración, vigilancia y mantenimiento del servicio de electricidad', '4.03.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.05.02.00', 'Servicio de administración, vigilancia y mantenimiento del servicio de gas', '4.03.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.01.00.00', 'Depreciación y amortización', '4.08.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.01.01.00', 'Depreciación', '4.08.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.01.01.01', 'Depreciación de edificios e instalaciones', '4.08.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.01.01.02', 'Depreciación de maquinaria y demás equipos de construcción, campo, industria y taller', '4.08.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.01.01.03', 'Depreciación de equipos de transporte, tracción y elevación', '4.08.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.01.01.04', 'Depreciación de equipos de comunicaciones y de señalamiento', '4.08.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.01.01.05', 'Depreciación de equipos médico-quirúrgicos, dentales y de veterinaria', '4.08.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.01.01.06', 'Depreciación de equipos científicos, religiosos, de enseñanza y recreación', '4.08.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.01.01.07', 'Depreciación de equipos para la seguridad pública', '4.08.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.01.01.08', 'Depreciación de máquinas, muebles y demás equipos de oficina y alojamiento', '4.08.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.01.01.09', 'Depreciación de semovientes', '4.08.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.01.01.99', 'Depreciación de otros bienes de uso', '4.08.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.01.02.00', 'Amortización ', '4.08.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.01.02.01', 'Amortización de marcas de fábrica y patentes de invención', '4.08.01.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.01.02.02', 'Amortización de derechos de autor', '4.08.01.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.01.02.03', 'Amortización de gastos de organización', '4.08.01.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.01.02.04', 'Amortización de paquetes y programas de computación', '4.08.01.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.01.02.05', 'Amortización de estudios y proyectos', '4.08.01.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.01.02.99', 'Amortización de otros activos intangibles', '4.08.01.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.02.00.00', 'Intereses por operaciones financieras', '4.08.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.02.01.00', 'Intereses por depósitos internos', '4.08.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.02.02.00', 'Intereses por títulos y valores', '4.08.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.02.03.00', 'Intereses por otros financiamientos', '4.08.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.03.00.00', 'Gastos por operaciones de seguro', '4.08.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.03.01.00', 'Gastos de siniestros', '4.08.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.03.02.00', 'Gastos de operaciones de  reaseguros', '4.08.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.03.99.00', 'Otros gastos de operaciones de seguro', '4.08.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.04.00.00', 'Pérdida en operaciones  de los servicios básicos', '4.08.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.04.01.00', 'Pérdidas en el proceso de distribución de los servicios ', '4.08.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.04.99.00', 'Otras pérdidas en operación', '4.08.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.05.00.00', 'Obligaciones en el ejercicio vigente', '4.08.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.05.01.00', 'Devoluciones de cobros indebidos', '4.08.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.05.02.00', 'Devoluciones y reintegros diversos', '4.08.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.05.03.00', 'Indemnizaciones diversas', '4.08.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.06.00.00', 'Pérdidas ajenas a la operación', '4.08.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.06.01.00', 'Pérdidas en inventarios', '4.08.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.06.02.00', 'Pérdidas en operaciones cambiarías', '4.08.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.06.03.00', 'Pérdidas en ventas de activos', '4.08.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.06.04.00', 'Pérdidas por cuentas incobrables', '4.08.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.06.05.00', 'Participación en pérdidas de otras empresas', '4.08.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.06.06.00', 'Pérdidas por auto-seguro', '4.08.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.06.07.00', 'Impuestos directos', '4.08.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.06.08.00', 'Intereses de mora ', '4.08.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.06.09.00', 'Reservas técnicas', '4.08.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.07.00.00', 'Descuentos, bonificaciones y devoluciones', '4.08.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.07.01.00', 'Descuentos sobre ventas', '4.08.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.07.02.00', 'Bonificaciones por ventas', '4.08.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.07.03.00', 'Devoluciones por ventas', '4.08.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.07.04.00', 'Devoluciones por primas de seguro', '4.08.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.08.00.00', 'Indemnizaciones y sanciones pecuniarias ', '4.08.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.08.01.00', 'Indemnizaciones por daños y perjuicios', '4.08.08.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.08.01.01', 'Indemnizaciones por daños y perjuicios ocasionados por organismos de la República, del Poder Estadal y del Poder Municipal', '4.08.08.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.08.01.02', 'Indemnizaciones por daños y perjuicios ocasionados por entes descentralizados sin fines empresariales', '4.08.08.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.08.01.03', 'Indemnizaciones por daños y perjuicios ocasionados por entes descentralizados con fines empresariales', '4.08.08.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.08.02.00', 'Sanciones pecuniarias', '4.08.08.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.08.02.01', 'Sanciones pecuniarias impuestas a los organismos de la República, del Poder Estadal y del Poder Municipal ', '4.08.08.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.08.02.02', 'Sanciones pecuniarias impuestas a los entes descentralizados sin fines empresariales', '4.08.08.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.08.02.03', 'Sanciones pecuniarias ocasionadas por entes descentralizados con fines empresariales', '4.08.08.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.99.00.00', 'Otros gastos', '4.08.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.99.01.00', 'Otros gastos', '4.08.99.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.00.00.00', 'ASIGNACIONES NO DISTRIBUIDAS', '4.00.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.01.00.00', 'Asignaciones no distribuidas de la Asamblea Nacional', '4.09.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.01.01.00', 'Asignaciones no distribuidas de la Asamblea Nacional', '4.09.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.02.00.00', 'Asignaciones no distribuidas de la Contraloría General de la República ', '4.09.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.02.01.00', 'Asignaciones no distribuidas de la Contraloría General de la República ', '4.09.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.03.00.00', 'Asignaciones no distribuidas del Consejo Nacional Electoral ', '4.09.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.03.01.00', 'Asignaciones no distribuidas del Consejo Nacional Electoral ', '4.09.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.04.00.00', 'Asignaciones no distribuidas del Tribunal Supremo de Justicia ', '4.09.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.04.01.00', 'Asignaciones no distribuidas del Tribunal Supremo de Justicia ', '4.09.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.05.00.00', 'Asignaciones no distribuidas del Ministerio Público ', '4.09.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.05.01.00', 'Asignaciones no distribuidas del Ministerio Público ', '4.09.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.06.00.00', 'Asignaciones no distribuidas de la Defensoría del Pueblo ', '4.09.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.06.01.00', 'Asignaciones no distribuidas de la Defensoría del Pueblo ', '4.09.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.07.00.00', 'Asignaciones no distribuidas del Consejo Moral Republicano ', '4.09.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.00.00.00.00', 'EGRESOS', '4.00.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.00.00.00', 'GASTOS DE PERSONAL', '4.00.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.01.00.00', 'Sueldos, salarios y otras retribuciones', '4.01.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.01.01.00', 'Sueldos básicos personal fijo a tiempo completo', '4.01.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.01.02.00', 'Sueldos básicos personal fijo a tiempo parcial', '4.01.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.01.03.00', 'Suplencias a empleados', '4.01.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.01.08.00', 'Sueldo al personal en trámite de nombramiento', '4.01.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.01.09.00', 'Remuneraciones al personal en período de disponibilidad', '4.01.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.01.10.00', 'Salarios a obreros en puestos permanentes a tiempo completo', '4.01.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.01.11.00', 'Salarios a obreros en puestos permanentes a tiempo parcial', '4.01.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.01.12.00', 'Salarios a obreros en puestos no permanentes', '4.01.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.01.13.00', 'Suplencias a obreros', '4.01.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.01.18.00', 'Remuneraciones al personal contratado ', '4.01.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.01.19.00', 'Retribuciones por becas-salarios, bolsas de trabajo, pasantías y similares', '4.01.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.01.20.00', 'Sueldo del personal militar profesional', '4.01.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.01.21.00', 'Sueldo o ración del personal militar no profesional', '4.01.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.01.22.00', 'Sueldo del personal militar de reserva', '4.01.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.01.29.00', 'Dietas', '4.01.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.01.30.00', 'Retribución al personal de reserva', '4.01.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.01.35.00', 'Sueldo básico de los altos funcionarios y altas funcionarias del poder público y de elección popular', '4.01.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.01.36.00', 'Sueldo básico del personal de alto nivel y de dirección', '4.01.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.01.37.00', 'Dietas de los altos funcionarios y altas funcionarias del poder público y de elección popular', '4.01.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.01.38.00', 'Dietas del personal de alto nivel y de dirección', '4.01.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.01.99.00', 'Otras retribuciones', '4.01.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.02.00.00', 'Compensaciones previstas en las escalas de sueldos y salarios  ', '4.01.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.02.01.00', 'Compensaciones previstas en las escalas de sueldos al personal empleado fijo a tiempo completo', '4.01.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.02.02.00', 'Compensaciones previstas en las escalas de sueldos al personal empleado fijo a tiempo parcial', '4.01.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.02.03.00', 'Compensaciones previstas en las escalas de salarios al personal obrero fijo a tiempo completo', '4.01.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.02.04.00', 'Compensaciones previstas en las escalas de salarios al personal obrero fijo a tiempo parcial', '4.01.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.02.05.00', 'Compensaciones previstas en las escalas de sueldos al personal militar', '4.01.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.02.06.00', 'Compensaciones previstas en las escalas de sueldos de los altos funcionarios y altas funcionarias del poder público y de elección popular', '4.01.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.02.07.00', 'Compensaciones previstas en las escalas de sueldos del personal de alto nivel y de dirección', '4.01.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.03.00.00', 'Primas', '4.01.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.03.01.00', 'Primas por mérito a empleados', '4.01.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.03.02.00', 'Primas de transporte a empleados', '4.01.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.03.03.00', 'Primas por hogar a empleados', '4.01.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.03.04.00', 'Primas por hijos a empleados', '4.01.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.03.05.00', 'Primas por alquileres a empleados', '4.01.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.03.06.00', 'Primas por residencia a empleados', '4.01.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.03.07.00', 'Primas por categoría de escuelas a empleados ', '4.01.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.03.08.00', 'Primas de profesionalización a empleados', '4.01.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.03.09.00', 'Primas por antigüedad a empleados', '4.01.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.03.10.00', 'Primas por jerarquía o responsabilidad en el cargo', '4.01.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.03.11.00', 'Primas al  personal en servicio en el exterior', '4.01.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.03.16.00', 'Primas por mérito a obreros ', '4.01.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.03.17.00', 'Primas de transporte a obreros ', '4.01.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.03.18.00', 'Primas por hogar a obreros ', '4.01.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.03.19.00', 'Primas por hijos de obreros ', '4.01.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.03.20.00', 'Primas por residencia a obreros', '4.01.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.03.21.00', 'Primas por antigüedad a obreros', '4.01.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.03.22.00', 'Primas de profesionalización a obreros', '4.01.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.03.26.00', 'Primas por hijos al personal militar', '4.01.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.03.27.00', 'Primas de profesionalización al personal militar ', '4.01.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.03.28.00', 'Primas por antigüedad al personal militar ', '4.01.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.03.29.00', 'Primas por potencial de ascenso al personal militar ', '4.01.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.03.30.00', 'Primas por frontera y sitios inhóspitos al personal militar y de seguridad', '4.01.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.03.31.00', 'Primas por riesgo al personal militar y de seguridad', '4.01.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.03.37.00', 'Primas de transporte al personal contratado', '4.01.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.03.38.00', 'Primas por hogar al personal contratado', '4.01.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.03.39.00', 'Primas por hijos al personal contratado', '4.01.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.03.40.00', 'Primas de profesionalización al personal contratado', '4.01.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.03.41.00', 'primas por antigüedad al personal contratado', '4.01.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.03.46.00', 'Primas a los altos funcionarios y altas funcionarias del poder público y de elección popular', '4.01.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.03.47.00', 'Primas al personal de alto nivel y de dirección', '4.01.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.03.94.00', 'Otras primas a los altos funcionarios y altas funcionarias del poder público y de elección popular', '4.01.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.03.95.00', 'Otras primas al personal de alto nivel y de dirección', '4.01.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.03.96.00', 'Otras primas al personal contratado', '4.01.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.03.97.00', 'Otras primas a empleados', '4.01.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.03.98.00', 'Otras primas a obreros', '4.01.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.03.99.00', 'Otras primas al personal militar', '4.01.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.04.00.00', 'Complementos de sueldos y salarios ', '4.01.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.04.01.00', 'Complemento a empleados por horas extraordinarias o por sobre tiempo ', '4.01.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.04.02.00', 'Complemento a empleados por trabajo nocturno ', '4.01.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.04.03.00', 'Complemento a empleados por gastos de alimentación ', '4.01.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.04.04.00', 'Complemento a empleados por gastos de transporte ', '4.01.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.04.05.00', 'Complemento a empleados por gastos de representación ', '4.01.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.04.06.00', 'Complemento a empleados por comisión de servicios ', '4.01.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.04.07.00', 'Bonificación a empleados', '4.01.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.04.08.00', 'Bono compensatorio de alimentación a empleados', '4.01.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.04.09.00', 'Bono compensatorio de transporte a empleados', '4.01.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.04.10.00', 'Complemento a empleados por días feriados', '4.01.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.04.14.00', 'Complemento a obreros por horas extraordinarias o por sobre tiempo ', '4.01.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.04.15.00', 'Complemento a obreros por trabajo o jornada nocturna ', '4.01.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.04.16.00', 'Complemento a obreros por gastos de alimentación ', '4.01.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.04.17.00', 'Complemento a obreros por gastos de transporte ', '4.01.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.04.18.00', 'Bono compensatorio de alimentación a obreros', '4.01.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.04.19.00', 'Bono compensatorio de transporte a obreros ', '4.01.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.04.20.00', 'Complemento a obreros por días feriados', '4.01.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.04.24.00', 'Complemento al personal contratado por horas extraordinarias o por sobre tiempo ', '4.01.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.04.25.00', 'Complemento al personal contratado por gastos de alimentación ', '4.01.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.04.26.00', 'Bono compensatorio de alimentación al personal contratado', '4.01.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.04.27.00', 'Bono compensatorio de transporte al personal contratado', '4.01.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.04.28.00', 'Complemento al personal contratado por días feriados', '4.01.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.04.32.00', 'Complemento al personal militar por gastos de alimentación ', '4.01.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.04.33.00', 'Complemento al personal militar por gastos de transporte ', '4.01.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.04.34.00', 'Complemento al personal militar en el exterior', '4.01.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.04.35.00', 'Bono compensatorio de alimentación al personal militar', '4.01.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.04.43.00', 'Complemento a altos funcionarios y altas funcionarias del poder público y de elección popular por gastos de representación', '4.01.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.04.44.00', 'Complemento a altos funcionarios y altas funcionarias del poder público y de elección popular por comisión de servicios', '4.01.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.04.45.00', 'Bonificación a altos funcionarios y altas funcionarias del poder público y de elección popular', '4.01.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.04.46.00', 'Bono compensatorio de alimentación a altos funcionarios y altas funcionarias del poder público y de elección popular', '4.01.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.04.47.00', 'Bono compensatorio de transporte a altos funcionarios y altas funcionarias del poder público y de elección popular', '4.01.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.04.48.00', 'Complemento al personal de alto nivel y de dirección por gastos de representación', '4.01.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.04.49.00', 'Complemento al personal de alto nivel y de dirección por comisión de servicios', '4.01.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.04.50.00', 'Bonificación al personal de alto nivel y de dirección', '4.01.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.04.51.00', 'Bono compensatorio de alimentación al personal de  alto nivel  y de dirección', '4.01.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.04.52.00', 'Bono compensatorio de transporte al personal de alto nivel y de dirección', '4.01.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.04.94.00', 'Otros complementos a altos funcionarios y altas funcionarias del sector público y de elección popular', '4.01.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.04.95.00', 'Otros complementos al personal de alto nivel y de dirección', '4.01.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.04.96.00', 'Otros complementos a empleados', '4.01.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.04.97.00', 'Otros complementos a obreros ', '4.01.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.04.98.00', 'Otros complementos al personal contratado', '4.01.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.04.99.00', 'Otros complementos al personal militar', '4.01.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.05.00.00', 'Aguinaldos, utilidades o  bonificación legal,  y bono vacacional ', '4.01.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.05.01.00', 'Aguinaldos a empleados', '4.01.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.05.02.00', 'Utilidades legales y convencionales a empleados', '4.01.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.05.03.00', 'Bono vacacional a empleados', '4.01.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.05.04.00', 'Aguinaldos a obreros ', '4.01.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.05.05.00', 'Utilidades legales y convencionales a obreros ', '4.01.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.05.06.00', 'Bono vacacional a obreros', '4.01.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.05.07.00', 'Aguinaldos al personal contratado', '4.01.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.05.08.00', 'Bono vacacional al personal contratado', '4.01.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.05.09.00', 'Aguinaldos al personal militar', '4.01.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.05.10.00', 'Bono vacacional al personal militar', '4.01.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.05.13.00', 'Aguinaldos a altos funcionarios y altas funcionarias del poder público y de elección popular', '4.01.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.05.14.00', 'Utilidades legales y convencionales a altos funcionarios y altas funcionarias del poder público y de elección popular', '4.01.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.05.15.00', 'Bono vacacional a altos funcionarios y altas funcionarias del poder público y de elección popular ', '4.01.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.05.16.00', 'Aguinaldos al personal de alto nivel y de dirección ', '4.01.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.05.17.00', 'Utilidades legales y convencionales al personal de alto nivel y de dirección', '4.01.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.05.18.00', 'Bono vacacional al personal de alto nivel y de dirección', '4.01.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.06.00.00', 'Aportes patronales y legales ', '4.01.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.06.01.00', 'Aporte patronal al Instituto Venezolano de los Seguros Sociales (IVSS) por empleados', '4.01.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.06.02.00', 'Aporte patronal al Instituto de Previsión y Asistencia Social para el personal del Ministerio de Educación (Ipasme)  por empleados', '4.01.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.06.03.00', 'Aporte patronal al Fondo de Jubilaciones por empleados', '4.01.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.06.04.00', 'Aporte patronal al Fondo de Seguro de Paro Forzoso por empleados', '4.01.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.06.05.00', 'Aporte patronal al Fondo de Ahorro Obligatorio para la Vivienda por empleados', '4.01.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.06.10.00', 'Aporte patronal al Instituto Venezolano de los Seguros Sociales (IVSS) por obreros', '4.01.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.06.11.00', 'Aporte patronal al Fondo de Jubilaciones por obreros', '4.01.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.06.12.00', 'Aporte patronal al Fondo de Seguro de Paro Forzoso por obreros', '4.01.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.06.13.00', 'Aporte patronal al Fondo de Ahorro Obligatorio para la Vivienda por obreros', '4.01.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.06.18.00', 'Aporte patronal a los organismos de seguridad social por los trabajadores locales empleados en las representaciones de Venezuela en el exterior', '4.01.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.06.19.00', 'Aporte patronal al Fondo de Ahorro Obligatorio para la Vivienda por personal militar', '4.01.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.06.25.00', 'Aporte legal al Instituto Venezolano de los Seguros Sociales (IVSS) por personal contratado', '4.01.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.06.26.00', 'Aporte legal al Fondo de Ahorro Obligatorio para la Vivienda por personal contratado', '4.01.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.06.31.00', 'Aporte patronal al Instituto Venezolano de los Seguros Sociales (IVSS) por altos funcionarios y altas funcionarias del poder público y de elección popular', '4.01.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.06.32.00', 'Aporte patronal al Instituto de Previsión y Asistencia Social para el personal del Ministerio de Educación (Ipasme)  por altos funcionarios y altas funcionarias del poder público y de elección popular', '4.01.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.06.33.00', 'Aporte patronal al Fondo de Jubilaciones por altos funcionarios y altas funcionarias del poder público y de elección popular', '4.01.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.06.34.00', 'Aporte patronal al Fondo de Ahorro Obligatorio para la Vivienda por altos funcionarios y altas funcionarias del poder público y de elección popular', '4.01.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.06.35.00', 'Aporte patronal al Fondo de Seguro de Paro Forzoso por altos funcionarios y altas funcionarias del poder público y de elección popular', '4.01.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.06.39.00', 'Aporte patronal al Instituto Venezolano de los Seguros Sociales (IVSS) por personal de alto nivel y de dirección', '4.01.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.06.40.00', 'Aporte patronal al Instituto de Previsión y Asistencia Social para el personal del Ministerio de Educación (Ipasme)  por personal de alto nivel y de dirección', '4.01.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.06.41.00', 'Aporte patronal al Fondo de Jubilaciones por personal de alto nivel y de dirección', '4.01.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.06.42.00', 'Aporte patronal al Fondo de Ahorro Obligatorio para la Vivienda por personal de alto nivel y de dirección', '4.01.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.06.43.00', 'Aporte patronal al Fondo de Seguro de Paro Forzoso por  personal de alto nivel y de dirección', '4.01.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.06.93.00', 'Otros aportes legales por altos funcionarios y altas funcionarias del poder público y de elección popular', '4.01.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.06.94.00', 'Otros aportes legales por el personal de alto nivel y de dirección', '4.01.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.06.95.00', 'Otros aportes legales por personal contratado', '4.01.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.06.96.00', 'Otros aportes legales por empleados ', '4.01.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.06.97.00', 'Otros aportes legales por obreros ', '4.01.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.06.98.00', 'Otros aportes legales por personal militar', '4.01.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.00.00', 'Asistencia socio-económica', '4.01.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.01.00', 'Capacitación y adiestramiento a empleados', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.02.00', 'Becas a empleados', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.03.00', 'Ayudas por matrimonio a empleados', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.04.00', 'Ayudas por nacimiento de hijos a empleados', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.05.00', 'Ayudas por defunción a empleados', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.06.00', 'Ayudas para medicinas, gastos médicos, odontológicos y de hospitalización a empleados', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.07.00', 'Aporte patronal a cajas de ahorro por empleados', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.08.00', 'Aporte patronal a los servicios de salud, accidentes personales y gastos funerarios por empleados', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.09.00', 'Ayudas a empleados para adquisición de uniformes y útiles escolares de sus hijos', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.10.00', 'Dotación de uniformes a empleados', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.11.00', 'Aporte patronal para gastos de guarderías y preescolar para hijos de empleados', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.12.00', 'Aportes para la adquisición de juguetes para los hijos del personal empleado', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.17.00', 'Capacitación y adiestramiento a obreros', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.18.00', 'Becas a obreros ', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.19.00', 'Ayudas por matrimonio de obreros ', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.20.00', 'Ayudas por nacimiento de hijos de obreros', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.21.00', 'Ayudas por defunción a obreros ', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.22.00', 'Ayudas para medicinas, gastos médicos, odontológicos y de hospitalización a obreros ', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.23.00', 'Aporte patronal a cajas de ahorro por obreros', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.24.00', 'Aporte patronal a los servicios de salud, accidentes personales y gastos funerarios por obreros', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.25.00', 'Ayudas a obreros para adquisición de uniformes y útiles escolares de sus hijos', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.26.00', 'Dotación de uniformes a obreros ', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.27.00', 'Aporte patronal para gastos de guarderías y preescolar para hijos de obreros', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.28.00', 'Aportes para la adquisición de juguetes para los hijos del personal obrero', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.33.00', 'Asistencia socio-económica al personal contratado', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.34.00', 'Capacitación y adiestramiento al personal militar', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.35.00', 'Becas al personal militar', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.36.00', 'Ayudas por matrimonio al personal militar', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.37.00', 'Ayudas por nacimiento de hijos al personal militar', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.38.00', 'Ayudas por defunción al personal militar', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.39.00', 'Ayudas para medicinas, gastos médicos, odontológicos y de hospitalización al personal militar', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.40.00', 'Aporte patronal a caja de ahorro por personal militar', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.41.00', 'Aporte patronal a los servicios de salud, accidentes personales y gastos funerarios personal militar', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.42.00', 'Ayudas al personal militar para adquisición de uniformes y útiles escolares de sus hijos', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.43.00', 'Aportes para la adquisición de juguetes para los hijos del personal  militar', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.44.00', 'Aporte patronal para gastos de guarderías y preescolar para hijos del personal militar', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.52.00', 'Capacitación y adiestramiento a altos funcionarios y altas funcionarias del poder público y de elección popular', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.53.00', 'Ayudas por matrimonio a altos funcionarios y altas funcionarias del poder público y de elección popular', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.54.00', 'Ayudas por nacimiento de hijos de altos funcionarios y altas funcionarias del poder público y de elección popular', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.55.00', 'Ayudas por defunción a altos funcionarios y altas funcionarias del poder público y de elección popular', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.56.00', 'Ayudas para medicinas, gastos médicos, odontológicos y de hospitalización a altos funcionarios del poder público y de elección popular', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.57.00', 'Aporte patronal a cajas de ahorro por altos funcionarios y altas funcionarias del poder público y de elección popular', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.58.00', 'Aporte patronal a los servicios de salud, accidentes personales y gastos funerarios por altos funcionarios y altas funcionarias del poder público y de elección popular', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.63.00', 'Capacitación y adiestramiento al personal de alto nivel y de dirección', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.64.00', 'Ayudas por matrimonio al personal de alto nivel y de dirección', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.65.00', 'Ayudas por nacimiento de hijos al personal de alto nivel y de dirección', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.66.00', 'Ayudas por defunción al personal de alto nivel y de dirección', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.67.00', 'Ayudas para medicinas, gastos médicos, odontológicos y de hospitalización al personal de alto nivel y de dirección', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.68.00', 'Aporte patronal a cajas de ahorro por personal de alto nivel y de dirección', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.69.00', 'Aporte patronal a los servicios de salud, accidentes personales y gastos funerarios por personal de alto nivel y de dirección', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.94.00', 'Otras subvenciones a altos funcionarios y altas funcionarias del poder público y de elección popular', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.95.00', 'Otras subvenciones al personal de alto nivel y de dirección', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.96.00', 'Otras subvenciones a empleados', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.97.00', 'Otras subvenciones a obreros', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.07.98.00', 'Otras subvenciones al personal militar', '4.01.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.08.00.00', 'Prestaciones sociales e indemnizaciones ', '4.01.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.08.01.00', 'Prestaciones sociales e indemnizaciones a empleados', '4.01.08.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.08.02.00', 'Prestaciones sociales e indemnizaciones a obreros', '4.01.08.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.08.03.00', 'Prestaciones sociales e indemnizaciones al personal contratado', '4.01.08.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.08.04.00', 'Prestaciones sociales e indemnizaciones al personal militar', '4.01.08.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.08.06.00', 'Prestaciones sociales e indemnizaciones a altos funcionarios y altas funcionarias del poder público y de elección popular', '4.01.08.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.08.07.00', 'Prestaciones sociales e indemnizaciones al personal de alto nivel y de dirección', '4.01.08.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.09.00.00', 'Capacitación y adiestramiento realizado por personal del organismo', '4.01.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.09.01.00', 'Capacitación y adiestramiento realizado por personal del organismo', '4.01.09.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.94.00.00', 'Otros gastos de los altos funcionarios y altas funcionarias del poder público y de elección popular', '4.01.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.94.01.00', 'Otros gastos de los altos funcionarios y altas funcionarias del poder público y de elección popular', '4.01.94.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.95.00.00', 'Otros gastos del personal de alto nivel y de dirección', '4.01.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.95.01.00', 'Otros gastos del personal de alto nivel y de dirección', '4.01.95.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.96.00.00', 'Otros gastos del personal empleado', '4.01.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.96.01.00', 'Otros gastos del personal empleado', '4.01.96.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.97.00.00', 'Otros gastos del personal obrero', '4.01.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.97.01.00', 'Otros gastos del personal obrero', '4.01.97.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.98.00.00', 'Otros gastos del personal militar', '4.01.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.01.98.01.00', 'Otros gastos del personal militar', '4.01.98.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.00.00.00', 'MATERIALES, SUMINISTROS Y MERCANCIAS', '4.00.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.01.00.00', 'Productos alimenticios y agropecuarios', '4.02.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.01.01.00', 'Alimentos y bebidas para personas', '4.02.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.01.02.00', 'Alimentos para animales', '4.02.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.01.03.00', 'Productos agrícolas y pecuarios', '4.02.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.01.04.00', 'Productos de la caza y pesca', '4.02.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.01.99.00', 'Otros productos alimenticios y agropecuarios', '4.02.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.02.00.00', 'Productos de minas, canteras y yacimientos', '4.02.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.02.01.00', 'Carbón mineral', '4.02.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.02.02.00', 'Petróleo crudo y gas natural', '4.02.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.02.03.00', 'Mineral de hierro', '4.02.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.02.04.00', 'Mineral no ferroso', '4.02.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.02.05.00', 'Piedra, arcilla, arena y tierra', '4.02.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.02.06.00', 'Mineral para la fabricación de productos químicos', '4.02.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.02.07.00', 'Sal  para uso industrial', '4.02.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.02.99.00', 'Otros productos de minas,  canteras y yacimientos', '4.02.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.03.00.00', 'Textiles y vestuarios', '4.02.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.03.01.00', 'Textiles', '4.02.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.03.02.00', 'Prendas de vestir', '4.02.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.03.03.00', 'Calzados', '4.02.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.03.99.00', 'Otros productos textiles y vestuarios', '4.02.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.04.00.00', 'Productos de cuero y caucho', '4.02.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.04.01.00', 'Cueros y pieles', '4.02.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.04.02.00', 'Productos de cuero y sucedáneos del cuero', '4.02.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.04.03.00', ' Cauchos y tripas para vehículos', '4.02.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.04.99.00', 'Otros productos de cuero y caucho', '4.02.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.05.00.00', 'Productos de papel, cartón e impresos', '4.02.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.05.01.00', 'Pulpa de madera, papel y cartón', '4.02.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.05.02.00', 'Envases y cajas de papel y cartón', '4.02.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.05.03.00', 'Productos de papel y cartón para oficina', '4.02.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.05.04.00', 'Libros, revistas y periódicos', '4.02.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.05.05.00', 'Material de enseñanza', '4.02.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.05.06.00', 'Productos de papel y cartón para computación', '4.02.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.05.07.00', 'Productos de papel y cartón para la imprenta y reproducción', '4.02.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.05.99.00', 'Otros productos de pulpa, papel y cartón', '4.02.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.06.00.00', 'Productos químicos y derivados', '4.02.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.06.01.00', 'Sustancias químicas y de uso  industrial', '4.02.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.02.06.02.00', 'Abonos, plaguicidas y otros', '4.02.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.05.03.00', 'Servicio de administración, vigilancia y mantenimiento del servicio de agua', '4.03.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.05.04.00', 'Servicio de administración, vigilancia y mantenimiento del servicio de teléfonos', '4.03.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.05.05.00', 'Servicio de administración, vigilancia y mantenimiento del servicio de comunicaciones', '4.03.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.05.06.00', 'Servicio de administración, vigilancia y mantenimiento del servicio de aseo urbano y domiciliario', '4.03.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.06.00.00', 'Servicios de transporte y almacenaje', '4.03.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.06.01.00', 'Fletes y embalajes', '4.03.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.06.02.00', 'Almacenaje', '4.03.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.06.03.00', 'Estacionamiento', '4.03.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.06.04.00', 'Peaje', '4.03.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.06.05.00', 'Servicios de protección en traslado de fondos y de mensajería', '4.03.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.07.00.00', 'Servicios de información, impresión y relaciones públicas', '4.03.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.07.01.00', 'Publicidad y propaganda', '4.03.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.07.02.00', 'Imprenta y reproducción', '4.03.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.07.03.00', 'Relaciones sociales', '4.03.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.07.04.00', 'Avisos', '4.03.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.08.00.00', 'Primas y otros gastos de seguros y comisiones bancarias', '4.03.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.08.01.00', 'Primas y gastos de seguros', '4.03.08.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.08.02.00', 'Comisiones y gastos bancarios', '4.03.08.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.08.03.00', 'Comisiones y gastos de adquisición de seguros', '4.03.08.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.09.00.00', 'Viáticos y pasajes', '4.03.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.09.01.00', 'Viáticos y pasajes dentro del país', '4.03.09.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.09.02.00', 'Viáticos y pasajes fuera del país', '4.03.09.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.09.03.00', 'Asignación por kilómetros recorridos', '4.03.09.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.10.00.00', 'Servicios profesionales y técnicos', '4.03.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.10.01.00', 'Servicios jurídicos', '4.03.10.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.10.02.00', 'Servicios de contabilidad y auditoria', '4.03.10.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.10.03.00', 'Servicios de procesamiento de datos', '4.03.10.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.10.04.00', 'Servicios de ingeniería y arquitectónicos', '4.03.10.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.10.05.00', 'Servicios médicos, odontológicos y otros servicios de sanidad', '4.03.10.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.10.06.00', 'Servicios de veterinaria', '4.03.10.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.10.07.00', 'Servicios de capacitación y adiestramiento', '4.03.10.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.10.08.00', 'Servicios presupuestarios', '4.03.10.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.10.09.00', 'Servicios de lavandería y tintorería', '4.03.10.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.10.10.00', 'Servicios de vigilancia', '4.03.10.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.10.11.00', 'Servicios para la elaboración y suministro de comida', '4.03.10.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.10.99.00', 'Otros servicios profesionales y técnicos', '4.03.10.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.11.00.00', 'Conservación y reparaciones menores de maquinaria y equipos', '4.03.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.11.01.00', 'Conservación y reparaciones menores de maquinaria y demás equipos de construcción, campo, industria y taller', '4.03.11 .00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.11.02.00', 'Conservación y reparaciones menores de equipos de transporte, tracción y elevación', '4.03.11.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.11.03.00', 'Conservación y reparaciones menores de equipos de comunicaciones y de señalamiento', '4.03.11 .00.01');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.11.04.00', 'Conservación y reparaciones menores de equipos médico- quirúrgicos, dentales y de veterinaria', '4.03.11.00.01');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.11.05.00', 'Conservación y reparaciones menores de equipos científicos, religiosos, de enseñanza y recreación', '4.03.11 .00.02');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.11.06.00', 'Conservación y reparaciones menores de equipos y armamentos de orden público, seguridad y defensa nacional', '4.03.11.00.02');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.11.07.00', 'Conservación y reparaciones menores de máquinas, muebles y demás equipos de oficina y alojamiento', '4.03.11 .00.03');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.11.99.00', 'Conservación y reparaciones menores de otras maquinaria y equipos', '4.03.11.00.03');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.12.00.00', 'Conservación y reparaciones menores de obras', '4.03.00 .00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.12.01.00', 'Conservación y reparaciones menores de obras en bienes del dominio privado', '4.03.12.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.12.02.00', 'Conservación y reparaciones menores de obras en bienes del dominio público', '4.03.12.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.13.00.00', 'Servicios de construcciones temporales', '4.03.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.13.01.00', 'Servicios de construcciones temporales', '4.03.13.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.14.00.00', 'Servicios de construcción de edificaciones para la venta', '4.03.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.14.01.00', 'Servicios de construcción de edificaciones para la venta', '4.03.14.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.15.00.00', 'Servicios fiscales', '4.03.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.15.01.00', 'Derechos de importación y servicios aduaneros', '4.03.15.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.15.02.00', 'Tasas y otros derechos obligatorios', '4.03.15.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.15.03.00', 'Asignación a agentes de especies fiscales', '4.03.15.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.15.99.00', 'Otros servicios fiscales', '4.03.15.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.16.00.00', 'Servicios de diversión, esparcimiento y culturales', '4.03.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.16.01.00', 'Servicios de diversión, esparcimiento y culturales', '4.03.16.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.17.00.00', 'Servicios de gestión administrativa prestados por organismos de asistencia técnica', '4.03.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.17.01.00', 'Servicios de gestión administrativa prestados por organismos de asistencia técnica', '4.03.17.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.18.00.00', 'Impuestos indirectos', '4.03.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.18.01.00', 'Impuesto al valor agregado', '4.03.18.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.18.99.00', 'Otros impuestos indirectos', '4.03.18.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.19.00.00', 'Comisiones por servicios para cumplir con los beneficios sociales', '4.03.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.19.01.00', 'Comisiones por servicios para cumplir con los beneficios sociales', '4.03.19.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.99.00.00', 'Otros servicios no personales', '4.03.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.03.99.01.00', 'Otros servicios no personales', '4.03.99.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.00.00.00', 'ACTIVOS REALES', '4.00.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.01.00.00', 'Repuestos y reparaciones mayores', '4.04.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.01.01.00', 'Repuestos mayores', '4.04.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.01.01.01', 'Repuestos mayores para maquinaria y demás equipos de construcción, campo, industria y taller', '4.04.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.01.01.02', 'Repuestos mayores para equipos de transporte, tracción y elevación', '4.04.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.01.01.03', 'Repuestos mayores para equipos de comunicaciones y de señalamiento', '4.04.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.01.01.04', 'Repuestos mayores para equipos médico-quirúrgicos, dentales y de  veterinaria', '4.04.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.01.01.05', 'Repuestos mayores para equipos científicos, religiosos, de enseñanza y recreación', '4.04.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.01.01.06', 'Repuestos mayores para equipos y armamentos de orden público, seguridad y defensa', '4.04.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.01.01.07', 'Repuestos mayores para máquinas, muebles y demás equipos de oficina y alojamiento', '4.04.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.01.01.99', 'Repuestos mayores para otras maquinaria y equipos', '4.04.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.01.02.00', 'Reparaciones mayores de maquinaria y equipos', '4.04.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.01.02.01', 'Reparaciones mayores de maquinaria y demás equipos de construcción, campo, industria y taller', '4.04.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.01.02.02', 'Reparaciones mayores de equipos de transporte, tracción y elevación', '4.04.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.01.02.03', 'Reparaciones mayores de equipos de comunicaciones y de señalamiento', '4.04.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.01.02.04', 'Reparaciones mayores de equipos médico-quirúrgicos, dentales y de veterinaria', '4.04.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.01.02.05', 'Reparaciones mayores de equipos científicos, religiosos, de enseñanza y recreación', '4.04.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.01.02.06', 'Reparaciones mayores de equipos y armamentos de orden público, seguridad y defensa nacional', '4.04.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.01.02.07', 'Reparaciones mayores de máquinas, muebles y demás equipos de oficina y alojamiento', '4.04.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.01.02.99', 'Reparaciones mayores de otras maquinaria y equipos ', '4.04.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.02.00.00', 'Conservación, ampliaciones y mejoras mayores de obras', '4.04.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.02.01.00', 'Conservación, ampliaciones y mejoras mayores de obras en bienes del dominio privado', '4.04.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.02.02.00', 'Conservación, ampliaciones y mejoras mayores de obras en bienes del dominio público', '4.04.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.03.00.00', 'Maquinaria y  demás equipos de construcción, campo, industria y taller', '4.04.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.03.01.00', 'Maquinaria y demás equipos de construcción y mantenimiento', '4.04.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.03.02.00', 'Maquinaria y equipos para mantenimiento de automotores', '4.04.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.03.03.00', 'Maquinaria y equipos agrícolas y pecuarios', '4.04.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.03.04.00', 'Maquinaria y equipos de artes gráficas y reproducción', '4.04.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.03.05.00', 'Maquinaria y equipos industriales y de taller', '4.04.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.03.06.00', 'Maquinaria y equipos de energía', '4.04.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.03.07.00', 'Maquinaria y equipos de riego y acueductos', '4.04.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.03.08.00', 'Equipos de almacén', '4.04.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.03.99.00', 'Otra maquinaria y demás equipos de construcción, campo, industria y taller', '4.04.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.04.00.00', 'Equipos de transporte, tracción y elevación', '4.04.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.04.01.00', 'Vehículos automotores terrestres', '4.04.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.04.02.00', 'Equipos ferroviarios y de cables aéreos', '4.04.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.04.03.00', 'Equipos marítimos de transporte', '4.04.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.04.04.00', 'Equipos aéreos de transporte', '4.04.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.04.05.00', 'Vehículos de tracción no motorizados', '4.04.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.04.06.00', 'Equipos auxiliares de transporte', '4.04.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.04.99.00', 'Otros equipos de transporte, tracción y elevación', '4.04.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.05.00.00', 'Equipos de comunicaciones y de señalamiento', '4.04.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.05.01.00', 'Equipos de telecomunicaciones', '4.04.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.05.02.00', 'Equipos de señalamiento', '4.04.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.05.03.00', 'Equipos de control de tráfico aéreo', '4.04.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.05.04.00', 'Equipos de correo', '4.04.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.05.99.00', 'Otros equipos de comunicaciones y de señalamiento', '4.04.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.06.00.00', 'Equipos médico-quirúrgicos, dentales y de veterinaria', '4.04.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.06.01.00', 'Equipos médico-quirúrgicos, dentales y de veterinaria', '4.04.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.06.99.00', 'Otros equipos médico-quirúrgicos, dentales y de veterinaria', '4.04.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.07.00.00', 'Equipos científicos, religiosos, de enseñanza y recreación', '4.04.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.07.01.00', 'Equipos científicos y de laboratorio', '4.04.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.07.02.00', 'Equipos de enseñanza, deporte y recreación', '4.04.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.07.03.00', 'Obras de arte', '4.04.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.07.04.00', 'Libros, revistas y otros instrumentos de enseñanzas', '4.04.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.07.05.00', 'Equipos religiosos', '4.04.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.07.06.00', 'Instrumentos musicales y equipos de audio', '4.04.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.07.99.00', 'Otros equipos científicos, religiosos, de enseñanza y recreación', '4.04.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.08.00.00', 'Equipos y armamentos de orden público, seguridad y defensa ', '4.04.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.08.01.00', 'Equipos y armamentos de orden público, seguridad y defensa nacional', '4.04.08.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.08.02.00', 'Equipos y armamentos de seguridad para la custodia y resguardo personal', '4.04.08.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.08.99.00', 'Otros equipos y armamentos de orden público, seguridad y defensa ', '4.04.08.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.09.00.00', 'Máquinas, muebles y demás equipos de oficina y alojamiento', '4.04.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.09.01.00', 'Mobiliario y equipos de oficina', '4.04.09.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.09.02.00', 'Equipos de computación', '4.04.09.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.09.03.00', 'Mobiliario y equipos de alojamiento', '4.04.09.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.09.99.00', 'Otras máquinas, muebles y demás equipos de oficina y alojamiento', '4.04.09.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.10.00.00', 'Semovientes', '4.04.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.10.01.00', 'Semovientes', '4.04.10.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.11.00.00', 'Inmuebles, maquinaria  y  equipos usados', '4.04.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.11.01.00', 'Adquisición de tierras y terrenos', '4.04.11.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.11.02.00', 'Adquisición de edificios e instalaciones', '4.04.11.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.11.03.00', 'Expropiación de tierras y terrenos', '4.04.11.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.11.04.00', 'Expropiación de edificios e instalaciones', '4.04.11.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.11.05.00', 'Adquisición de maquinaria y equipos usados', '4.04.11.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.11.05.01', 'Maquinaria y demás equipos de construcción, campo, industria y taller', '4.04.11.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.11.05.02', 'Equipos de transporte, tracción y elevación', '4.04.11.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.11.05.03', 'Equipos de comunicaciones y de señalamiento', '4.04.11.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.11.05.04', 'Equipos médico-quirúrgicos, dentales y de veterinaria', '4.04.11.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.11.05.05', 'Equipos científicos, religiosos, de enseñanza y recreación', '4.04.11.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.11.05.06', 'Equipos para seguridad pública', '4.04.11.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.11.05.07', 'Máquinas, muebles y demás equipos de oficina y alojamiento', '4.04.11.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.11.05.99', 'Otras maquinaria y equipos usados', '4.04.11.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.12.00.00', 'Activos intangibles', '4.04.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.12.01.00', 'Marcas de fábrica y patentes de invención', '4.04.12.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.12.02.00', 'Derechos de autor', '4.04.12.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.12.03.00', 'Gastos de organización', '4.04.12.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.12.04.00', 'Paquetes y programas de computación', '4.04.12.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.12.05.00', 'Estudios y proyectos', '4.04.12.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.12.99.00', 'Otros activos intangibles', '4.04.12.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.13.00.00', 'Estudios y proyectos para inversión en activos fijos', '4.04.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.13.01.00', 'Estudios y proyectos aplicables a bienes del dominio privado ', '4.04.13.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.13.02.00', 'Estudios y proyectos aplicables a bienes del dominio público', '4.04.13.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.14.00.00', 'Contratación de inspección de obras', '4.04.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.14.01.00', 'Contratación de inspección de obras de bienes del dominio privado', '4.04.14.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.14.02.00', 'Contratación de inspección de obras de bienes del dominio público', '4.04.14.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.15.00.00', 'Construcciones del dominio privado       ', '4.04.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.15.01.00', 'Construcciones de edificaciones médico-asistenciales', '4.04.15.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.15.02.00', 'Construcciones de edificaciones militares y de seguridad', '4.04.15.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.15.03.00', 'Construcciones de edificaciones educativas, religiosas y recreativas', '4.04.15.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.15.04.00', 'Construcciones de edificaciones culturales y deportivas', '4.04.15.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.15.05.00', 'Construcciones de edificaciones para oficina', '4.04.15.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.15.06.00', 'Construcciones de edificaciones industriales', '4.04.15.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.15.07.00', 'Construcciones de edificaciones habitacionales', '4.04.15.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.15.99.00', 'Otras construcciones del dominio privado', '4.04.15.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.16.00.00', 'Construcciones del dominio público   ', '4.04.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.16.01.00', 'Construcción de  vialidad', '4.04.16.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.16.02.00', 'Construcción de plazas, parques y similares', '4.04.16.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.16.03.00', 'Construcciones  de instalaciones hidráulicas', '4.04.16.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.16.04.00', 'Construcciones de puertos y aeropuertos', '4.04.16.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.16.99.00', 'Otras construcciones del dominio público', '4.04.16.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.99.00.00', 'Otros activos reales', '4.04.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.04.99.01.00', 'Otros activos reales', '4.04.99.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.00.00.00', 'ACTIVOS FINANCIEROS', '4.00.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.01.00.00', 'Aportes en acciones y participaciones de capital', '4.05.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.01.01.00', 'Aportes en acciones y participaciones de capital al sector privado ', '4.05.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.01.02.00', 'Aportes en acciones y participaciones de capital al sector público ', '4.05.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.01.02.01', 'Aportes en acciones y participaciones de capital a entes descentralizados sin fines empresariales', '4.05.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.01.02.02', 'Aportes en acciones y participaciones de capital a instituciones de protección social', '4.05.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.01.02.03', 'Aportes en acciones y participaciones de capital a entes descentralizados con fines empresariales  petroleros', '4.05.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.01.02.04', 'Aportes en acciones y participaciones de capital a entes descentralizados con fines empresariales no petroleros', '4.05.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.01.02.05', 'Aportes en acciones y participaciones de capital a entes descentralizados financieros bancarios', '4.05.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.01.02.06', 'Aportes en acciones y participaciones de capital a entes descentralizados financieros no bancarios', '4.05.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.01.02.07', 'Aportes en acciones y participaciones de capital a organismos del sector público para el pago de su deuda', '4.05.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.01.03.00', 'Aportes en acciones y participaciones de capital al sector externo', '4.05.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.01.03.01', 'Aportes en acciones y participaciones de capital a organismos internacionales ', '4.05.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.01.03.99', 'Otros aportes en acciones y participaciones de capital al sector externo', '4.05.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.02.00.00', 'Adquisición de títulos y valores que no otorgan propiedad', '4.05.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.02.01.00', 'Adquisición de títulos y valores  a corto plazo', '4.05.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.02.01.01', 'Adquisición de títulos y valores privados', '4.05.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.02.01.02', 'Adquisición de títulos y valores públicos', '4.05.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.02.01.03', 'Adquisición de títulos y valores externos ', '4.05.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.02.02.00', 'Adquisición de títulos y valores a largo plazo', '4.05.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.02.02.01', 'Adquisición de títulos y valores privados', '4.05.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.02.02.02', 'Adquisición de títulos y valores públicos', '4.05.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.02.02.03', 'Adquisición de títulos y valores externos ', '4.05.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.03.00.00', 'Concesión de préstamos a corto plazo', '4.05.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.03.01.00', 'Concesión de préstamos al sector privado a corto plazo', '4.05.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.03.02.00', 'Concesión de préstamos al sector público a corto plazo', '4.05.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.03.02.01', 'Concesión de préstamos a la República', '4.05.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.03.02.02', 'Concesión de préstamos a entes descentralizados sin fines empresariales', '4.05.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.03.02.03', 'Concesión de préstamos a instituciones de protección social', '4.05.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.03.02.04', 'Concesión de préstamos a entes descentralizados con fines empresariales petroleros', '4.05.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.03.02.05', 'Concesión de préstamos a entes descentralizados con fines empresariales no petroleros', '4.05.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.03.02.06', 'Concesión de préstamos a entes descentralizados financieros bancarios', '4.05.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.03.02.07', 'Concesión de préstamos a entes descentralizados financieros no bancarios', '4.05.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.03.02.08', 'Concesión de préstamos al Poder Estadal ', '4.05.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.03.02.09', 'Concesión de préstamos al Poder Municipal ', '4.05.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.03.03.00', 'Concesión de préstamos al sector externo a corto plazo', '4.05.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.03.03.01', 'Concesión de préstamos a instituciones sin fines de lucro', '4.05.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.03.03.02', 'Concesión de préstamos a gobiernos extranjeros', '4.05.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.03.03.03', 'Concesión de préstamos a organismos internacionales', '4.05.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.04.00.00', 'Concesión de préstamos a largo plazo', '4.05.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.04.01.00', 'Concesión de préstamos al sector privado a largo plazo', '4.05.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.04.02.00', 'Concesión de préstamos al sector público a largo plazo', '4.05.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.04.02.01', 'Concesión de préstamos a la República', '4.05.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.04.02.02', 'Concesión de préstamos a entes descentralizados sin fines empresariales', '4.05.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.04.02.03', 'Concesión de préstamos a instituciones de protección social', '4.05.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.04.02.04', 'Concesión de préstamos a entes descentralizados con fines empresariales petroleros', '4.05.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.04.02.05', 'Concesión de préstamos a entes descentralizados con fines empresariales no petroleros', '4.05.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.04.02.06', 'Concesión de préstamos a entes descentralizados financieros bancarios', '4.05.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.04.02.07', 'Concesión de préstamos a entes descentralizados financieros no bancarios', '4.05.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.04.02.08', 'Concesión de préstamos al Poder Estadal', '4.05.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.04.02.09', 'Concesión de préstamos al Poder Municipal', '4.05.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.04.03.00', 'Concesión de préstamos al sector externo a largo plazo', '4.05.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.04.03.01', 'Concesión de préstamos a instituciones sin fines de lucro', '4.05.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.04.03.02', 'Concesión de préstamos a gobiernos extranjeros', '4.05.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.04.03.03', 'Concesión de préstamos a organismos internacionales', '4.05.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.05.00.00', 'Incremento de disponibilidades', '4.05.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.05.01.00', 'Incremento en caja ', '4.05.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.05.02.00', 'Incremento en bancos', '4.05.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.05.02.01', 'Incremento en bancos públicos', '4.05.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.05.02.02', 'Incremento en bancos privados', '4.05.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.05.02.03', 'Incremento en bancos del exterior', '4.05.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.05.03.00', 'Incremento de inversiones temporales', '4.05.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.06.00.00', 'Incremento de cuentas por cobrar a corto plazo', '4.05.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.06.01.00', 'Incremento de cuentas comerciales por cobrar a corto plazo', '4.05.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.06.02.00', 'Incremento de rentas por recaudar a corto plazo', '4.05.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.06.03.00', 'Incremento de deudas por rendir', '4.05.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.06.03.01', 'Incremento de deudas por rendir de fondos en avance', '4.05.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.06.03.02', 'Incremento de deudas por rendir de fondos en anticipo', '4.05.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.06.99.00', 'Incremento de otras cuentas por cobrar a corto plazo', '4.05.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.07.00.00', 'Incremento de efectos por cobrar a corto plazo', '4.05.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.07.01.00', 'Incremento de efectos comerciales por cobrar a corto plazo', '4.05.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.07.99.00', 'Incremento de otros efectos por cobrar a corto plazo', '4.05.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.08.00.00', 'Incremento de cuentas por cobrar a mediano y  largo plazo', '4.05.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.08.01.00', 'Incremento de cuentas comerciales por cobrar a mediano y  largo plazo', '4.05.08.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.08.02.00', 'Incremento de rentas por recaudar a mediano y  largo plazo', '4.05.08.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.08.99.00', 'Incremento de otras cuentas por cobrar a mediano y  largo plazo', '4.05.08.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.09.00.00', 'Incremento de efectos por cobrar a  mediano y  largo plazo', '4.05.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.09.01.00', 'Incremento de efectos comerciales por cobrar a mediano y  largo plazo', '4.05.09.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.09.99.00', 'Incremento de otros efectos por cobrar a mediano y  largo plazo', '4.05.09.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.10.00.00', 'Incremento de fondos en avance, en anticipos y en fideicomiso', '4.05.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.10.01.00', 'Incremento de fondos en avance', '4.05.10.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.10.02.00', 'Incremento de fondos en anticipos ', '4.05.10.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.10.03.00', 'Incremento de fondos en fideicomiso', '4.05.10.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.10.04.00', 'Incremento de anticipos a proveedores', '4.05.10.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.10.05.00', 'Incremento de anticipos a contratistas por contratos de  corto plazo', '4.05.10.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.10.06.00', 'Incremento de anticipos a contratistas por contratos de mediano y largo plazo', '4.05.10.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.11.00.00', 'Incremento de activos diferidos a corto plazo', '4.05.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.11.01.00', 'Incremento de gastos a corto plazo pagados por anticipado ', '4.05.11.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.11.01.01', 'Incremento de intereses de la deuda pública interna a corto plazo pagados por anticipado', '4.05.11.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.11.01.02', 'Incremento de intereses de la deuda pública externa a corto plazo pagados por anticipado', '4.05.11.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.11.01.03', 'Incremento de otros intereses a corto plazo pagados por anticipado ', '4.05.11.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.11.01.04', 'Incremento de débitos por apertura de carta de crédito a corto plazo', '4.05.11.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.11.01.99', 'Incremento de otros gastos a corto plazo pagados por anticipado ', '4.05.11.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.11.02.00', 'Incremento de depósitos otorgados en garantía a corto plazo', '4.05.11.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.11.99.00', 'Incremento de otros activos diferidos a corto plazo', '4.05.11.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.12.00.00', 'Incremento de activos diferidos a mediano y largo plazo', '4.05.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.12.01.00', 'Incremento de gastos a mediano y largo plazo pagados por anticipado ', '4.05.12.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.12.01.01', 'Incremento de intereses de la deuda pública interna a  largo plazo pagados por anticipado', '4.05.12.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.12.01.02', 'Incremento de intereses de la deuda pública externa a largo plazo pagados por anticipado', '4.05.12.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.12.01.08', 'Incremento de otros intereses a mediano y largo plazo pagados por anticipado ', '4.05.12.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.12.01.99', 'Incremento de otros gastos a mediano y largo plazo pagados por anticipado ', '4.05.12.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.12.02.00', 'Incremento de depósitos otorgados en garantía a mediano y largo plazo', '4.05.12.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.12.99.00', 'Incremento de otros activos diferidos a mediano y largo plazo', '4.05.12.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.13.00.00', 'Incremento del Fondo de Estabilización Macroeconómica (FEM)', '4.05.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.13.01.00', 'Incremento del Fondo de Estabilización Macroeconómica (FEM) de la  República', '4.05.13.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.13.02.00', 'Incremento del Fondo de Estabilización Macroeconómica (FEM) del Poder Estadal  ', '4.05.13.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.13.03.00', 'Incremento del Fondo de Estabilización Macroeconómica (FEM) del Poder Municipal', '4.05.13.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.14.00.00', 'Incremento del Fondo de Ahorro Intergeneracional', '4.05.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.14.01.00', 'Incremento del Fondo de Ahorro Intergeneracional', '4.05.14.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.16.00.00', 'Incremento del Fondo de Aportes del Sector Público ', '4.05.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.16.01.00', 'Incremento del Fondo de Aportes del Sector Público ', '4.05.16.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.20.00.00', 'Incremento de otros activos financieros circulantes', '4.05.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.20.01.00', 'Incremento de otros activos financieros circulantes', '4.05.20.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.21.00.00', 'Incremento de otros activos financieros no circulantes', '4.05.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.21.01.00', 'Incremento de activos en gestión judicial a mediano y largo plazo', '4.05.21.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.21.02.00', 'Incremento de títulos y otros valores de la deuda pública en litigio a largo plazo', '4.05.21.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.21.99.00', 'Incremento de otros activos financieros no circulantes', '4.05.21.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.99.00.00', 'Otros activos financieros', '4.05.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.05.99.01.00', 'Otros activos financieros', '4.05.99.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.06.00.00.00', 'GASTOS DE DEFENSA Y SEGURIDAD DEL ESTADO', '4.00.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.06.01.00.00', 'Gastos de defensa y seguridad del Estado', '4.06.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.06.01.01.00', 'Gastos de defensa y seguridad del Estado', '4.06.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.00.00.00', 'TRANSFERENCIAS Y DONACIONES', '4.00.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.00.00', 'Transferencias y donaciones corrientes internas', '4.07.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.00', 'Transferencias corrientes internas al sector privado', '4.07.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.01', 'Pensiones del personal empleado, obrero y militar', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.02', 'Jubilaciones del personal empleado, obrero y militar', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.03', 'Becas escolares', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.04', 'Becas universitarias en el país ', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.05', 'Becas de perfeccionamiento profesional en el país', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.06', 'Becas para estudios en el extranjero', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.07', 'Otras becas', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.08', 'Previsión por accidentes de trabajo', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.09', 'Aguinaldos al personal empleado, obrero y militar pensionado ', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.10', 'Aportes a caja de ahorro del personal empleado, obrero y militar pensionado ', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.11', 'Aportes a los servicios de salud, accidentes personales y gastos funerarios del personal empleado, obrero y militar pensionado', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.12', 'Otras subvenciones socio-económicas del personal empleado, obrero y militar pensionado', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.13', 'Aguinaldos al personal empleado, obrero y militar jubilado', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.14', 'Aportes a caja de ahorro del personal empleado, obrero y militar jubilado', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.15', 'Aportes a los servicios de salud, accidentes personales y gastos funerarios del personal empleado, obrero y militar jubilado', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.16', 'Otras subvenciones socio-económicas del personal empleado, obrero y militar jubilado', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.30', 'Incapacidad temporal sin hospitalización', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.31', 'Incapacidad temporal con hospitalización ', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.32', 'Reposo por maternidad ', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.33', 'Indemnización por paro forzoso', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.34', 'Otros tipos de incapacidad temporal ', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.35', 'Indemnización por comisión por pensiones', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.36', 'Indemnización por  comisión por cesantía', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.37', 'Incapacidad parcial', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.38', 'Invalidez ', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.39', 'Pensiones por vejez, viudez y orfandad', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.40', 'Indemnización por cesantía', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.41', 'Otras pensiones y demás prestaciones en dinero', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.42', 'Incapacidad parcial por accidente común', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.43', 'Incapacidad parcial por enfermedades profesionales', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.44', 'Incapacidad parcial por accidente de trabajo', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.45', 'Indemnización única por invalidez', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.46', 'Indemnización única por vejez', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.47', 'Sobrevivientes por enfermedad común', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.48', 'Sobrevivientes por accidente común', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.49', 'Sobrevivientes por enfermedades profesionales ', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.50', 'Sobrevivientes por accidentes de trabajo', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.51', 'Indemnizaciones por conmutación de renta', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.52', 'Indemnizaciones por conmutación de pensiones', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.53', 'Indemnizaciones por comisión de renta', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.54', 'Asignación por nupcias  ', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.55', 'Asignación por funeraria', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.56', 'Otras asignaciones', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.70', 'Subsidios educacionales al sector privado', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.71', 'Subsidios a universidades privadas', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.72', 'Subsidios culturales al sector privado', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.73', 'Subsidios a instituciones benéficas privadas', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.74', 'Subsidios a centros de empleados', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.75', 'Subsidios a organismos laborales y gremiales', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.76', 'Subsidios a entidades religiosas', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.77', 'Subsidios a entidades deportivas y recreativas de carácter privado', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.78', 'Subsidios científicos al sector privado', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.79', 'Subsidios  a cooperativas', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.80', 'Subsidios a empresas privadas', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.01.99', 'Otras transferencias corrientes internas al sector privado', '4.07.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.02.00', 'Donaciones corrientes internas al sector privado', '4.07.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.02.01', 'Donaciones corrientes a personas', '4.07.01.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.02.02', 'Donaciones corrientes a instituciones sin fines de lucro', '4.07.01.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.03.00', 'Transferencias corrientes internas al sector público', '4.07.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.03.01', 'Transferencias corrientes a la República', '4.07.01.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.03.02', 'Transferencias corrientes a entes descentralizados sin fines empresariales ', '4.07.01.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.03.03', 'Transferencias corrientes a entes descentralizados sin fines empresariales para atender beneficios de la seguridad social', '4.07.01.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.03.04', 'Transferencias corrientes a instituciones de protección social ', '4.07.01.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.03.05', 'Transferencias corrientes a instituciones de protección social para atender beneficios de la seguridad social ', '4.07.01.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.03.06', 'Transferencias corrientes a entes descentralizados con fines empresariales petroleros', '4.07.01.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.03.07', 'Transferencias corrientes a entes descentralizados con fines empresariales no petroleros', '4.07.01.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.03.08', 'Transferencias corrientes a entes descentralizados financieros bancarios', '4.07.01.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.03.09', 'Transferencias corrientes a entes descentralizados financieros no bancarios', '4.07.01.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.03.10', 'Transferencias corrientes al Poder Estadal', '4.07.01.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.03.11', 'Transferencias corrientes al Poder Municipal', '4.07.01.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.03.13', 'Subsidios otorgados por normas externas  ', '4.07.01.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.03.14', 'Incentivos otorgados por normas externas  ', '4.07.01.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.03.15', 'Subsidios otorgados por precios políticos', '4.07.01.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.03.16', 'Subsidios de costos sociales por normas externas', '4.07.01.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.03.99', 'Otras transferencias corrientes internas al sector público ', '4.07.01.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.04.00', 'Donaciones corrientes internas al sector público', '4.07.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.04.01', 'Donaciones corrientes a la República', '4.07.01.04.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.04.02', 'Donaciones corrientes a  entes descentralizados sin fines empresariales', '4.07.01.04.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.04.03', 'Donaciones corrientes a instituciones de protección social', '4.07.01.04.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.04.04', 'Donaciones corrientes a entes descentralizados con fines empresariales petroleros', '4.07.01.04.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.04.05', 'Donaciones corrientes a entes descentralizados con fines empresariales no petroleros', '4.07.01.04.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.04.06', 'Donaciones corrientes a entes descentralizados financieros bancarios', '4.07.01.04.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.04.07', 'Donaciones corrientes a entes descentralizados financieros no bancarios', '4.07.01.04.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.04.08', 'Donaciones corrientes al Poder Estadal', '4.07.01.04.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.04.09', 'Donaciones corrientes al Poder Municipal', '4.07.01.04.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.05.00', 'Pensiones de altos funcionarios y altas funcionarias del poder público y de elección popular, del personal de alto nivel y de dirección', '4.07.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.05.01', 'Pensiones de altos funcionarios y altas funcionarias del poder público y de elección popular', '4.07.01.05.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.05.02', 'Pensiones del personal de alto nivel y de dirección', '4.07.01.05.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.05.06', 'Aguinaldos de altos funcionarios y altas funcionarias del poder público y de elección popular pensionados', '4.07.01.05.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.05.07', 'Aguinaldos del personal pensionado de alto nivel y de dirección', '4.07.01.05.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.05.11', 'Aportes a caja de ahorro de altos funcionarios y altas funcionarias del poder público y de elección popular pensionados', '4.07.01.05.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.05.12', 'Aportes a caja de ahorro del personal pensionado de alto nivel y de dirección ', '4.07.01.05.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.05.16', 'Aportes a los servicios de salud, accidentes personales y gastos funerarios de altos funcionarios y altas funcionarias del poder público y de elección popular pensionados', '4.07.01.05.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.05.17', 'Aportes a los servicios de salud, accidentes personales y gastos funerarios del personal pensionado de alto nivel y de dirección', '4.07.01.05.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.05.98', 'Otras subvenciones de altos funcionarios y altas funcionarias del poder público y de elección popular pensionados', '4.07.01.05.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.05.99', 'Otras subvenciones del personal pensionado de alto nivel y de dirección', '4.07.01.05.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.06.00', 'Jubilaciones de altos funcionarios y altas funcionarias del poder público y de elección popular, del personal de alto nivel y de dirección', '4.07.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.06.01', 'Jubilaciones de altos funcionarios y altas funcionarias del poder público y de elección popular', '4.07.01.06.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.06.02', 'Jubilaciones del personal de alto nivel y de dirección', '4.07.01.06.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.06.06', 'Aguinaldos de altos funcionarios y altas funcionarias del poder público y de elección popular jubilados', '4.07.01.06.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.06.07', 'Aguinaldos del personal jubilado de alto nivel y de dirección', '4.07.01.06.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.06.11', 'Aportes a caja de ahorro de altos funcionarios y altas funcionarias del poder público y de elección popular jubilados', '4.07.01.06.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.06.12', 'Aportes a caja de ahorro del personal  jubilado de alto nivel y de dirección', '4.07.01.06.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.06.16', 'Aportes a los servicios de salud, accidentes personales y gastos funerarios de altos funcionarios y altas funcionarias del poder público y de elección popular jubilados', '4.07.01.06.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.06.17', 'Aportes a los servicios de salud, accidentes personales y gastos funerarios del personal jubilado de alto nivel y de dirección', '4.07.01.06.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.06.98', 'Otras subvenciones de altos funcionarios y altas funcionarias del poder público y de elección popular jubilados', '4.07.01.06.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.01.06.99', 'Otras subvenciones del personal jubilado de alto nivel y de dirección', '4.07.01.06.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.02.00.00', 'Transferencias y donaciones corrientes al  exterior', '4.07.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.02.01.00', 'Transferencias corrientes al  exterior', '4.07.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.02.01.01', 'Becas de capacitación e investigación en el exterior', '4.07.02.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.02.01.02', 'Transferencias corrientes a instituciones sin fines de lucro', '4.07.02.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.02.01.03', 'Transferencias corrientes a gobiernos extranjeros', '4.07.02.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.02.01.04', 'Transferencias corrientes a organismos internacionales', '4.07.02.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.02.02.00', 'Donaciones corrientes al exterior', '4.07.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.02.02.01', 'Donaciones corrientes a personas', '4.07.02.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.02.02.02', 'Donaciones corrientes a instituciones sin fines de lucro', '4.07.02.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.02.02.03', 'Donaciones corrientes a gobiernos extranjeros', '4.07.02.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.02.02.04', 'Donaciones corrientes a organismos internacionales', '4.07.02.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.03.00.00', 'Transferencias y donaciones de capital internas', '4.07.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.03.01.00', 'Transferencias de capital internas al sector privado ', '4.07.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.03.01.01', 'Transferencias de capital a personas', '4.07.03.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.03.01.02', 'Transferencias de capital a instituciones sin fines de lucro', '4.07.03.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.03.01.03', 'Transferencias de capital a empresas privadas ', '4.07.03.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.03.02.00', 'Donaciones de capital internas al sector privado', '4.07.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.03.02.01', 'Donaciones de capital a personas', '4.07.03.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.03.02.02', 'Donaciones de capital a instituciones sin fines de lucro', '4.07.03.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.03.03.00', 'Transferencias de capital internas  al sector público', '4.07.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.03.03.01', 'Transferencias de capital a la República', '4.07.03.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.03.03.02', 'Transferencias de capital a  entes descentralizados sin fines empresariales', '4.07.03.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.03.03.03', 'Transferencias de capital a Instituciones de protección social', '4.07.03.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.03.03.04', 'Transferencias de capital a entes descentralizados con fines empresariales petroleros', '4.07.03.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.03.03.05', 'Transferencias de capital a entes descentralizados con fines empresariales no petroleros', '4.07.03.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.03.03.06', 'Transferencias de capital a entes descentralizados financieros bancarios', '4.07.03.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.03.03.07', 'Transferencias de capital a entes descentralizados financieros no bancarios', '4.07.03.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.03.03.08', 'Transferencias de capital al Poder Estadal', '4.07.03.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.03.03.09', 'Transferencias de capital al Poder Municipal', '4.07.03.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.03.03.99', 'Otras transferencias de capital internas al sector público ', '4.07.03.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.03.04.00', 'Donaciones de capital internas al sector público ', '4.07.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.03.04.01', 'Donaciones de capital a la República', '4.07.03.04.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.03.04.02', 'Donaciones de capital a entes descentralizados sin fines empresariales', '4.07.03.04.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.03.04.03', 'Donaciones de capital a Instituciones de protección social', '4.07.03.04.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.03.04.04', 'Donaciones de capital a entes descentralizados con fines empresariales petroleros', '4.07.03.04.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.03.04.05', 'Donaciones de capital a entes descentralizados con fines empresariales no petroleros', '4.07.03.04.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.03.04.06', 'Donaciones de capital a entes descentralizados financieros bancarios', '4.07.03.04.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.03.04.07', 'Donaciones de capital a entes descentralizados financieros no bancarios', '4.07.03.04.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.03.04.08', 'Donaciones de capital al Poder Estadal', '4.07.03.04.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.03.04.09', 'Donaciones de capital al Poder Municipal', '4.07.03.04.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.04.00.00', 'Transferencias y donaciones de capital al exterior', '4.07.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.04.01.00', 'Transferencias de capital al exterior', '4.07.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.04.01.01', 'Transferencias de capital a personas', '4.07.04.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.04.01.02', 'Transferencias de capital a instituciones sin fines de lucro', '4.07.04.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.04.01.03', 'Transferencias de capital a gobiernos extranjeros', '4.07.04.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.04.01.04', 'Transferencias de capital a organismos internacionales', '4.07.04.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.04.02.00', 'Donaciones de capital al exterior', '4.07.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.04.02.01', 'Donaciones de capital a personas', '4.07.04.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.04.02.02', 'Donaciones de capital a instituciones sin fines de lucro', '4.07.04.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.04.02.03', 'Donaciones de capital a gobiernos extranjeros', '4.07.04.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.04.02.04', 'Donaciones de capital a organismos internacionales ', '4.07.04.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.05.00.00', 'Situado ', '4.07.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.05.01.00', 'Situado Constitucional', '4.07.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.05.01.01', 'Situado  Estadal', '4.07.05.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.05.01.02', 'Situado  Municipal', '4.07.05.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.05.02.00', 'Situado Estadal a Municipal', '4.07.05.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.06.00.00', 'Subsidio de Régimen Especial', '4.07.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.06.01.00', 'Subsidio de Régimen Especial', '4.07.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.07.00.00', 'Subsidio de Capitalidad', '4.07.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.07.01.00', 'Subsidio de Capitalidad', '4.07.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.08.00.00', 'Asignaciones Económicas Especiales ( LAEE ) ', '4.07.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.08.01.00', 'Asignaciones Económicas Especiales (LAEE) Estadal', '4.07.08.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.08.02.00', 'Asignaciones Económicas Especiales (LAEE) Estadal a Municipal', '4.07.08.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.08.03.00', 'Asignaciones Económicas Especiales (LAEE) Municipal', '4.07.08.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.08.04.00', 'Asignaciones Económicas Especiales (LAEE) Fondo Nacional de los Consejos Comunales', '4.07.08.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.08.05.00', 'Asignaciones Económicas Especiales (LAEE) Apoyo al Fortalecimiento Institucional', '4.07.08.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.09.00.00', 'Aportes al Poder Estadal y al Poder Municipal  por transferencia de servicios', '4.07.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.09.01.00', 'Aportes al Poder Estadal por transferencia de servicios', '4.07.09.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.09.02.00', 'Aportes al Poder Municipal por transferencia de servicios', '4.07.09.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.10.00.00', 'Fondo Intergubernamental para la Descentralización ( FIDES)', '4.07.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.10.01.00', 'Fondo Intergubernamental para la Descentralización ( FIDES )  ', '4.07.10.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.11.00.00', 'Fondo de Compensación Interterritorial', '4.07.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.11.01.00', 'Fondo de Compensación Interterritorial Estadal', '4.07.11.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.11.02.00', 'Fondo de Compensación Interterritorial Municipal', '4.07.11.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.11.03.00', 'Fondo de Compensación Interterritorial Poder Popular', '4.07.11.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.11.04.00', 'Fondo de Compensación Interterritorial Fortalecimiento Institucional', '4.07.11.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.12.00.00', 'Transferencias y Donaciones a Consejos Comunales', '4.07.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.12.01.00', 'Transferencias y donaciones corrientes a Consejos Comunales', '4.07.12.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.12.01.01', 'Transferencias corrientes a Consejos Comunales', '4.07.12.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.12.01.02', 'Donaciones corrientes a Consejos Comunales', '4.07.12.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.12.02.00', 'Transferencias y donaciones de capital a Consejos Comunales', '4.07.12.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.12.02.01', 'Transferencias de capital a Consejos Comunales', '4.07.12.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.07.12.02.02', 'Donaciones de capital a Consejos Comunales', '4.07.12.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.08.00.00.00', 'OTROS GASTOS', '4.00.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.07.01.00', 'Asignaciones no distribuidas del Consejo Moral Republicano ', '4.09.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.08.00.00', 'Reestructuración de organismos del sector público ', '4.09.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.08.01.00', 'Reestructuración de organismos del sector público ', '4.09.08.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.09.00.00', 'Fondo de apoyo al trabajador y su grupo familiar ', '4.09.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.09.01.00', 'Fondo de apoyo al trabajador y su grupo familiar de la Administración Pública Nacional', '4.09.09.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.09.02.00', 'Fondo de apoyo al trabajador y su grupo familiar de las Entidades Federales, los Municipios y otras formas de gobierno municipal', '4.09.09.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.10.00.00', 'Reforma de la seguridad social', '4.09.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.10.01.00', 'Reforma de la seguridad social', '4.09.10.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.11.00.00', 'Emergencias en el territorio nacional', '4.09.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.11.01.00', 'Emergencias en el territorio nacional', '4.09.11.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.12.00.00', 'Fondo para la cancelación de pasivos laborales', '4.09.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.12.01.00', 'Fondo para la cancelación de pasivos laborales', '4.09.12.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.13.00.00', 'Fondo para la cancelación de deuda por servicios de electricidad, teléfono, aseo, agua, y condominio', '4.09.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.13.01.00', 'Fondo para la cancelación de deuda por servicios de electricidad, teléfono, aseo, agua y condominio de los organismos de la Administración Central', '4.09.13.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.13.02.00', 'Fondo para la cancelación de deuda por servicios de electricidad, teléfono, aseo, agua y condominio de los organismos de la Administración Descentralizada Nacional', '4.09.13.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.14.00.00', 'Fondo para remuneraciones, pensiones y jubilaciones y otras retribuciones', '4.09.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.14.01.00', 'Fondo para remuneraciones, pensiones y jubilaciones y otras retribuciones', '4.09.14.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.15 00.00', 'Fondo para atender compromisos generados de la Ley Orgánica del Trabajo, los Trabajadores y las Trabajadoras', '4.09.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.15.01.00', 'Fondo para atender compromisos generados de la Ley Orgánica del Trabajo, los Trabajadores y las Trabajadoras', '4.09.15.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.16.00.00', 'Asignaciones para cancelar compromisos pendientes de ejercicios anteriores', '4.09.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.16.01.00', 'Asignaciones para cancelar compromisos pendientes de ejercicios anteriores', '4.09.16.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.17.00.00', 'Asignaciones para cancelar la deuda Fogade – Ministerio competente en Materia de Finanzas - Banco Central de Venezuela (BCV)', '4.09.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.17.01.00', 'Asignaciones para cancelar la deuda Fogade – Ministerio competente en Materia de Finanzas - Banco Central de Venezuela (BCV)', '4.09.17.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.18.00.00', 'Asignaciones para atender los gastos de la referenda y elecciones', '4.09.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.18.01.00', 'Asignaciones para atender los gastos de la referenda y elecciones', '4.09.18.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.19.00.00', 'Asignaciones para atender los gastos por honorarios profesionales de bufetes internacionales, costas y costos judiciales', '4.09.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.19.01.00', 'Asignaciones para atender los gastos por honorarios profesionales de bufetes internacionales, costas y costos judiciales', '4.09.19.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.20.00.00', 'Fondo para atender compromisos generados por la contratación colectiva', '4.09.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.20.01.00', 'Fondo para atender compromisos generados por la contratación colectiva', '4.09.20.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.21.00.00', 'Proyecto social especial', '4.09.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.21.01.00', 'Proyecto social especial', '4.09.21.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.22.00.00', 'Asignaciones para programas y proyectos financiados con recursos de organismos multilaterales y/o bilaterales', '4.09.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.22.01.00', 'Asignaciones para programas y proyectos financiados con recursos de organismos multilaterales y/o bilaterales', '4.09.22.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.23.00.00', 'Asignación para facilitar la preparación de proyectos', '4.09.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.23.01.00', 'Asignación para facilitar la preparación de proyectos', '4.09.23.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.24.00.00', 'Programas de inversión para las entidades estadales, municipalidades y otras instituciones', '4.09.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.24.01.00', 'Programas de inversión para las entidades estadales, municipalidades y otras instituciones', '4.09.24.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.25.00.00', 'Cancelación de compromisos', '4.09.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.25.01.00', 'Cancelación de compromisos', '4.09.25.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.26.00.00', 'Asignaciones para atender gastos de los organismos del sector público', '4.09.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.26.01.00', 'Asignaciones para atender gastos de los organismos del sector público', '4.09.26.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.27.00.00', 'Convenio de cooperación especial', '4.09.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.09.27.01.00', 'Convenio de cooperación especial', '4.09.27.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.00.00.00', 'SERVICIO DE LA DEUDA PÚBLICA ', '4.00.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.00.00', 'Servicio de la deuda pública interna a corto plazo', '4.10.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.01.00', 'Servicio de la deuda pública interna a corto plazo de títulos y valores', '4.10.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.01.01', 'Amortización de la deuda pública interna a corto plazo de títulos y valores', '4.10.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.01.02', 'Amortización de la deuda pública interna a corto plazo de letras del tesoro', '4.10.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.01.03', 'Intereses de la deuda pública interna a corto plazo de títulos y valores', '4.10.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.01.04', 'Intereses por mora y multas de la deuda pública interna a corto plazo de títulos y valores', '4.10.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.01.05', 'Comisiones y otros gastos de la deuda pública interna a corto plazo de títulos y valores', '4.10.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.01.06', 'Descuentos en colocación de títulos y valores de la deuda pública interna a  corto plazo', '4.10.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.01.07', 'Descuentos en colocación de letras del tesoro a corto plazo  ', '4.10.01.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.02.00', 'Servicio de la deuda pública interna por préstamos a corto plazo', '4.10.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.02.01', 'Amortización de la deuda pública interna por préstamos recibidos del sector privado a corto plazo ', '4.10.01.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.02.02', 'Amortización de la deuda pública interna por préstamos recibidos de la República a corto plazo ', '4.10.01.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.02.03', 'Amortización de la deuda pública interna por préstamos recibidos de entes descentralizados sin fines empresariales a corto plazo', '4.10.01.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.02.04', 'Amortización de la deuda pública interna por préstamos recibidos de instituciones de protección social a corto plazo', '4.10.01.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.02.05', 'Amortización de la deuda pública interna por préstamos recibidos de entes descentralizados con fines empresariales petroleros a corto plazo ', '4.10.01.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.02.06', 'Amortización de la deuda pública interna por préstamos recibidos de entes descentralizados con fines empresariales no petroleros a corto plazo ', '4.10.01.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.02.07', 'Amortización de la deuda pública interna por préstamos recibidos de entes descentralizados financieros bancarios a corto plazo  ', '4.10.01.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.02.08', 'Amortización de la deuda pública interna por préstamos recibidos de entes descentralizados financieros no bancarios a corto plazo  ', '4.10.01.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.02.09', 'Amortización de la deuda pública interna por préstamos recibidos del Poder Estadal a corto plazo ', '4.10.01.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.02.10', 'Amortización de la deuda pública interna por préstamos recibidos del Poder Municipal a corto plazo ', '4.10.01.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.02.11', 'Intereses de la deuda pública interna por préstamos recibidos del sector privado a corto plazo', '4.10.01.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.02.12', 'Intereses de la deuda pública interna por préstamos recibidos de la República a corto plazo', '4.10.01.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.02.13', 'Intereses de la deuda pública interna por préstamos recibidos de entes descentralizados sin fines empresariales a corto plazo', '4.10.01.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.02.14', 'Intereses de la deuda pública interna por préstamos recibidos de instituciones de protección social a corto plazo', '4.10.01.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.02.15', 'Intereses de la deuda pública interna por préstamos recibidos de entes descentralizados con fines empresariales petroleros a corto plazo ', '4.10.01.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.02.16', 'Intereses de la deuda pública interna por préstamos recibidos de entes descentralizados con fines empresariales no petroleros a corto plazo ', '4.10.01.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.02.17', 'Intereses de la deuda pública interna por préstamos recibidos de entes descentralizados financieros bancarios a corto plazo  ', '4.10.01.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.02.18', 'Intereses de la deuda pública interna por préstamos recibidos de entes descentralizados financieros no bancarios a corto plazo  ', '4.10.01.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.02.19', 'Intereses de la deuda pública interna por préstamos recibidos del Poder Estadal a corto plazo ', '4.10.01.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.02.20', 'Intereses de la deuda pública interna por préstamos recibidos del Poder Municipal a corto plazo ', '4.10.01.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.02.21', 'Intereses por mora y multas de la deuda pública interna por préstamos recibidos del sector privado a corto plazo', '4.10.01.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.02.22', 'Intereses por mora y multas de la deuda pública interna por préstamos recibidos de la República a corto plazo', '4.10.01.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.02.23', 'Intereses por mora y multas de la deuda pública interna por préstamos recibidos de entes descentralizados sin fines empresariales a corto plazo', '4.10.01.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.02.24', 'Intereses por mora y multas de la deuda pública interna por préstamos recibidos de instituciones de protección social a corto plazo', '4.10.01.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.02.25', 'Intereses por mora y multas de la deuda pública interna por préstamos recibidos de entes descentralizados con fines empresariales petroleros a corto plazo ', '4.10.01.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.02.26', 'Intereses por mora y multas de la deuda pública interna por préstamos recibidos de entes descentralizados con fines empresariales no petroleros a corto plazo ', '4.10.01.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.02.27', 'Intereses por mora y multas de la deuda pública interna por préstamos recibidos de entes descentralizados financieros bancarios a corto plazo  ', '4.10.01.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.02.28', 'Intereses por mora y multas de la deuda pública interna por préstamos recibidos de entes descentralizados financieros no bancarios a corto plazo  ', '4.10.01.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.02.29', 'Intereses por mora y multas de la deuda pública interna por préstamos recibidos del Poder Estadal a corto plazo ', '4.10.01.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.02.30', 'Intereses por mora y multas de la deuda pública interna por préstamos recibidos del Poder Municipal a corto plazo ', '4.10.01.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.02.31', 'Comisiones y otros gastos de la deuda pública interna por préstamos recibidos del sector privado a corto plazo', '4.10.01.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.02.32', 'Comisiones y otros gastos de la deuda pública interna por préstamos recibidos de la República a corto plazo', '4.10.01.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.02.33', 'Comisiones y otros gastos de la deuda pública interna por préstamos recibidos de entes descentralizados sin fines empresariales a corto plazo', '4.10.01.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.02.34', 'Comisiones y otros gastos de la deuda pública interna por préstamos recibidos de instituciones de protección social a corto  plazo', '4.10.01.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.02.35', 'Comisiones y otros gastos de la deuda pública interna por préstamos recibidos de entes descentralizados con fines empresariales petroleros a corto plazo ', '4.10.01.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.02.36', 'Comisiones y otros gastos de la deuda pública interna por préstamos recibidos de entes descentralizados con fines empresariales no petroleros a corto plazo ', '4.10.01.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.02.37', 'Comisiones y otros gastos de la deuda pública interna por préstamos recibidos de entes descentralizados financieros bancarios a corto plazo  ', '4.10.01.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.02.38', 'Comisiones y otros gastos de la deuda pública interna por préstamos recibidos de entes descentralizados financieros no bancarios a corto  plazo  ', '4.10.01.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.02.39', 'Comisiones y otros gastos de la deuda pública interna por préstamos recibidos del Poder Estadal a corto plazo ', '4.10.01.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.02.40', 'Comisiones y otros gastos de la deuda pública interna por préstamos recibidos del Poder Municipal a corto  plazo ', '4.10.01.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.03.00', 'Servicio de la deuda pública interna indirecta por préstamos a corto plazo', '4.10.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.03.01', 'Amortización de la deuda pública interna indirecta por préstamos recibidos del sector privado a corto plazo', '4.10.01.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.03.02', 'Amortización de la deuda pública interna indirecta por préstamos recibidos del sector público a corto plazo', '4.10.01.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.03.03', 'Intereses de la deuda pública interna indirecta por préstamos recibidos del sector privado a corto plazo', '4.10.01.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.03.04', 'Intereses de la deuda pública interna indirecta por préstamos recibidos del sector público a corto plazo', '4.10.01.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.03.05', 'Intereses por mora y multas de la deuda pública interna indirecta por préstamos recibidos del sector privado a corto plazo', '4.10.01.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.03.06', 'Intereses por mora y multas de la deuda pública interna indirecta por préstamos recibidos del sector público a corto plazo', '4.10.01.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.03.07', 'Comisiones y otros gastos de la deuda pública interna indirecta por préstamos recibidos del sector privado a corto plazo', '4.10.01.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.04.02.00', 'Disminución de efectos por pagar a contratistas a corto plazo', '4.11.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.01.03.08', 'Comisiones y otros gastos de la deuda pública interna indirecta por préstamos recibidos del sector público a corto plazo', '4.10.01.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.00.00', 'Servicio de la deuda pública interna a largo plazo', '4.10.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.01.00', 'Servicio de la deuda pública interna a largo plazo de títulos y valores', '4.10.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.01.01', 'Amortización de la deuda pública interna a largo plazo de títulos y valores', '4.10.02.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.01.02', 'Amortización de la deuda pública interna a largo plazo de letras del tesoro', '4.10.02.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.01.03', 'Intereses de la deuda pública interna a largo plazo de títulos y valores', '4.10.02.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.01.04', 'Intereses por mora y multas de la deuda pública interna a largo plazo de títulos y valores', '4.10.02.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.01.05', 'Comisiones y otros gastos de la deuda pública interna a largo plazo de títulos y valores', '4.10.02.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.01.06', 'Descuentos en colocación de títulos y valores de la deuda pública interna a largo plazo', '4.10.02.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.01.07', 'Descuentos en colocación de letras del tesoro a largo plazo  ', '4.10.02.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.02.00', 'Servicio de la deuda pública interna por préstamos a largo plazo', '4.10.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.02.01', 'Amortización de la deuda pública interna por préstamos recibidos del sector privado a largo plazo ', '4.10.02.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.02.02', 'Amortización de la deuda pública interna por préstamos recibidos de la República a largo plazo ', '4.10.02.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.02.03', 'Amortización de la deuda pública interna por préstamos recibidos de entes descentralizados sin fines empresariales a largo plazo', '4.10.02.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.02.04', 'Amortización de la deuda pública interna por préstamos recibidos de instituciones de protección social a largo plazo', '4.10.02.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.02.05', 'Amortización de la deuda pública interna por préstamos recibidos de entes descentralizados con fines empresariales petroleros a largo plazo ', '4.10.02.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.02.06', 'Amortización de la deuda pública interna por préstamos recibidos de entes descentralizados con fines empresariales no petroleros a largo plazo ', '4.10.02.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.02.07', 'Amortización de la deuda pública interna por préstamos recibidos de entes descentralizados financieros bancarios a largo plazo  ', '4.10.02.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.02.08', 'Amortización de la deuda pública interna por préstamos recibidos de entes descentralizados financieros no bancarios a largo plazo  ', '4.10.02.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.02.09', 'Amortización de la deuda pública interna por préstamos recibidos del Poder Estadal a largo plazo ', '4.10.02.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.02.10', 'Amortización de la deuda pública interna por préstamos recibidos del Poder Municipal a largo plazo ', '4.10.02.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.02.11', 'Intereses de la deuda pública interna por préstamos recibidos del sector privado a largo plazo', '4.10.02.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.02.12', 'Intereses de la deuda pública interna por préstamos recibidos de la República a largo plazo', '4.10.02.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.02.13', 'Intereses de la deuda pública interna por préstamos recibidos de entes descentralizados sin fines empresariales a largo plazo', '4.10.02.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.02.14', 'Intereses de la deuda pública interna por préstamos recibidos de instituciones de protección social a largo plazo', '4.10.02.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.02.15', 'Intereses de la deuda pública interna por préstamos recibidos de entes descentralizados con fines empresariales petroleros a largo plazo ', '4.10.02.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.02.16', 'Intereses de la deuda pública interna por préstamos recibidos de entes descentralizados con fines empresariales no petroleros a largo plazo ', '4.10.02.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.02.17', 'Intereses de la deuda pública interna por préstamos recibidos de entes descentralizados financieros bancarios a largo plazo  ', '4.10.02.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.02.18', 'Intereses de la deuda pública interna por préstamos recibidos de entes descentralizados financieros no bancarios a largo plazo  ', '4.10.02.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.02.19', 'Intereses de la deuda pública interna por préstamos recibidos del Poder Estadal a largo plazo ', '4.10.02.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.02.20', 'Intereses de la deuda pública interna por préstamos recibidos del Poder Municipal a largo plazo ', '4.10.02.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.02.21', 'Intereses por mora y multas de la deuda pública interna por préstamos recibidos del sector privado a largo plazo', '4.10.02.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.02.22', 'Intereses por mora y multas de la deuda pública interna por préstamos recibidos de la República a largo plazo', '4.10.02.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.02.23', 'Intereses por mora y multas de la deuda pública interna por préstamos recibidos de entes descentralizados sin fines empresariales a largo plazo', '4.10.02.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.02.24', 'Intereses por mora y multas de la deuda pública interna por préstamos recibidos de instituciones de protección social a largo plazo', '4.10.02.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.02.25', 'Intereses por mora y multas de la deuda pública interna por préstamos recibidos de entes descentralizados con fines empresariales petroleros a largo plazo ', '4.10.02.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.02.26', 'Intereses por mora y multas de la deuda pública interna por préstamos recibidos de entes descentralizados con fines empresariales no petroleros a largo plazo ', '4.10.02.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.02.27', 'Intereses por mora y multas de la deuda pública interna por préstamos recibidos de entes descentralizados financieros bancarios a largo plazo  ', '4.10.02.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.02.28', 'Intereses por mora y multas de la deuda pública interna por préstamos recibidos de entes descentralizados financieros no bancarios a largo plazo  ', '4.10.02.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.02.29', 'Intereses por mora y multas de la deuda pública interna por préstamos recibidos del Poder Estadal a largo plazo ', '4.10.02.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.02.30', 'Intereses por mora y multas de la deuda pública interna por préstamos recibidos del Poder Municipal a largo plazo ', '4.10.02.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.02.31', 'Comisiones y otros gastos de la deuda pública interna por préstamos recibidos del sector privado a largo plazo', '4.10.02.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.02.32', 'Comisiones y otros gastos de la deuda pública interna por préstamos recibidos de la República a largo plazo', '4.10.02.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.02.33', 'Comisiones y otros gastos de la deuda pública interna por préstamos recibidos de entes descentralizados sin fines empresariales a largo plazo', '4.10.02.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.02.34', 'Comisiones y otros gastos de la deuda pública interna por préstamos recibidos de instituciones de protección social a largo plazo', '4.10.02.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.02.35', 'Comisiones y otros gastos de la deuda pública interna por préstamos recibidos de entes descentralizados con fines empresariales petroleros a largo plazo ', '4.10.02.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.02.36', 'Comisiones y otros gastos de la deuda pública interna por préstamos recibidos de entes descentralizados con fines empresariales no petroleros a largo plazo ', '4.10.02.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.02.37', 'Comisiones y otros gastos de la deuda pública interna por préstamos recibidos de entes descentralizados financieros bancarios a largo plazo  ', '4.10.02.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.02.38', 'Comisiones y otros gastos de la deuda pública interna por préstamos recibidos de entes descentralizados financieros no bancarios a largo plazo  ', '4.10.02.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.02.39', 'Comisiones y otros gastos de la deuda pública interna por préstamos recibidos del Poder Estadal a largo plazo ', '4.10.02.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.02.40', 'Comisiones y otros gastos de la deuda pública interna por préstamos recibidos del Poder Municipal a largo plazo ', '4.10.02.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.03.00', 'Servicio de la deuda pública interna indirecta a largo plazo de títulos y valores', '4.10.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.03.01', 'Amortización de la deuda pública interna indirecta a largo plazo de títulos y valores', '4.10.02.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.03.02', 'Intereses de la deuda pública interna indirecta a largo plazo de títulos y valores', '4.10.02.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.03.03', 'Intereses por mora y multas de la deuda pública interna indirecta a largo plazo de títulos y valores', '4.10.02.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.03.04', 'Comisiones y otros gastos de la deuda pública interna indirecta a largo plazo de títulos y valores', '4.10.02.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.03.05', 'Descuentos en colocación de títulos y valores de la deuda pública interna indirecta de largo plazo', '4.10.02.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.04.00', 'Servicio de la deuda pública interna indirecta por préstamos a largo plazo', '4.10.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.04.01', 'Amortización de la deuda pública interna indirecta por préstamos recibidos del sector privado a largo plazo  ', '4.10.02.04.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.04.02', 'Amortización de la deuda pública interna indirecta por préstamos recibidos del sector público a largo plazo  ', '4.10.02.04.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.04.03', 'Intereses de la deuda pública interna indirecta por préstamos recibidos del sector privado a largo plazo', '4.10.02.04.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.04.04', 'Intereses de la deuda pública interna indirecta por préstamos recibidos del sector público a largo plazo', '4.10.02.04.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.04.05', 'Intereses por mora y multas de la deuda pública interna indirecta por préstamos recibidos del sector privado a largo plazo', '4.10.02.04.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.04.06', 'Intereses por mora y multas de la deuda pública interna indirecta por préstamos recibidos del sector público a largo plazo', '4.10.02.04.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.04.07', 'Comisiones y otros gastos de la deuda pública interna indirecta por préstamos recibidos del sector privado a largo plazo', '4.10.02.04.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.02.04.08', 'Comisiones y otros gastos de la deuda pública interna indirecta por préstamos recibidos del sector público a largo plazo', '4.10.02.04.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.03.00.00', 'Servicio de la deuda pública externa a corto plazo', '4.10.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.03.01.00', 'Servicio de la deuda pública externa a corto plazo de títulos y valores', '4.10.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.03.01.01', 'Amortización de la deuda pública externa a corto plazo de títulos y valores', '4.10.03.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.03.01.02', 'Intereses de la deuda pública externa a corto plazo de títulos y valores', '4.10.03.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.03.01.03', 'Intereses por mora y multas de la deuda pública externa a corto plazo de títulos y valores', '4.10.03.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.03.01.04', 'Comisiones y otros gastos de la deuda pública externa a corto plazo de títulos y valores', '4.10.03.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.03.01.05', 'Descuentos en colocación de títulos y valores de la deuda pública externa a corto plazo', '4.10.03.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.03.02.00', 'Servicio de la deuda pública externa por préstamos a corto plazo', '4.10.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.03.02.01', 'Amortización de la deuda pública externa por préstamos recibidos de gobiernos extranjeros a corto plazo ', '4.10.03.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.03.02.02', 'Amortización de la deuda pública externa por préstamos recibidos de organismos internacionales a corto plazo', '4.10.03.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.03.02.03', 'Amortización de la deuda pública externa por préstamos recibidos de instituciones financieras externas a corto plazo', '4.10.03.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.03.02.04', 'Amortización de la deuda pública externa por préstamos recibidos de proveedores de bienes y servicios externos a corto plazo', '4.10.03.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.03.02.05', 'Intereses de la deuda pública externa por préstamos  recibidos de gobiernos extranjeros a corto plazo', '4.10.03.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.03.02.06', 'Intereses de la deuda pública externa por préstamos  recibidos de organismos internacionales a corto plazo', '4.10.03.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.03.02.07', 'Intereses de la deuda pública externa por préstamos  recibidos de instituciones financieras externas a corto plazo', '4.10.03.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.03.02.08', 'Intereses de la deuda pública externa por préstamos recibidos de proveedores de bienes y servicios externos a corto plazo', '4.10.03.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.03.02.09', 'Intereses por mora y multas de la deuda pública externa por préstamos recibidos de gobiernos extranjeros a corto plazo  ', '4.10.03.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.03.02.10', 'Intereses por mora y multas de la deuda pública externa por préstamos recibidos  de organismos internacionales a corto plazo', '4.10.03.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.03.02.11', 'Intereses por mora y multas de la deuda pública externa por préstamos recibidos de instituciones financieras externas a corto plazo', '4.10.03.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.03.02.12', 'Intereses por mora y multas de la deuda pública externa por préstamos recibidos de proveedores de bienes y servicios externos a corto plazo ', '4.10.03.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.03.02.13', 'Comisiones y otros gastos de la deuda pública externa por préstamos recibidos de gobiernos extranjeros a corto plazo  ', '4.10.03.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.03.02.14', 'Comisiones y otros gastos de la deuda pública externa por préstamos  recibidos  de organismos internacionales a corto plazo', '4.10.03.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.03.02.15', 'Comisiones y otros gastos de la deuda pública externa por préstamos  recibidos de instituciones financieras externas a corto plazo', '4.10.03.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.03.02.16', 'Comisiones y otros gastos de la deuda pública externa por préstamos recibidos de proveedores de bienes y servicios externos a corto plazo ', '4.10.03.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.03.03.00', 'Servicio de la deuda pública externa indirecta por préstamos a corto plazo', '4.10.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.03.03.01', 'Amortización de la deuda pública externa indirecta por préstamos recibidos de gobiernos extranjeros a corto plazo ', '4.10.03.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.03.03.02', 'Amortización de la deuda pública externa indirecta por préstamos recibidos de organismos internacionales a corto plazo', '4.10.03.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.04.03.00', 'Disminución de cuentas por pagar a contratistas a mediano y largo plazo ', '4.11.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.03.03.03', 'Amortización de la deuda pública externa indirecta por préstamos recibidos de instituciones financieras externas a corto plazo', '4.10.03.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.03.03.04', 'Amortización de la deuda pública externa indirecta por préstamos  recibidos de proveedores de bienes y servicios externos a corto plazo', '4.10.03.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.03.03.05', 'Intereses de la deuda pública externa indirecta por préstamos recibidos de gobiernos extranjeros a corto plazo', '4.10.03.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.03.03.06', 'Intereses de la deuda pública externa indirecta por préstamos recibidos de organismos internacionales a corto plazo', '4.10.03.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.03.03.07', 'Intereses de la deuda pública externa indirecta por préstamos recibidos de instituciones financieras externas a corto plazo', '4.10.03.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.03.03.08', 'Intereses de la deuda pública externa indirecta por préstamos recibidos de proveedores de bienes y servicios externos a corto plazo', '4.10.03.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.03.03.09', 'Intereses por mora y multas de la deuda pública externa indirecta por préstamos  recibidos de gobiernos extranjeros a corto plazo', '4.10.03.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.03.03.10', 'Intereses por mora y multas de la deuda pública externa indirecta por préstamos recibidos de organismos internacionales a corto plazo ', '4.10.03.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.03.03.11', 'Intereses por mora y multas de la deuda pública externa indirecta por préstamos recibidos de instituciones financieras externas a corto plazo ', '4.10.03.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.03.03.12', 'Intereses por mora y multas de la deuda pública externa indirecta por préstamos  recibidos de proveedores de bienes y servicios externos a corto plazo ', '4.10.03.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.03.03.13', 'Comisiones y otros gastos de la deuda pública externa indirecta por préstamos  recibidos de gobiernos extranjeros a corto plazo', '4.10.03.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.03.03.14', 'Comisiones y otros gastos de la deuda pública externa indirecta por préstamos recibidos de organismos internacionales a corto plazo ', '4.10.03.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.03.03.15', 'Comisiones y otros gastos de la deuda pública externa indirecta por préstamos  recibidos de instituciones financieras externas a corto plazo', '4.10.03.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.03.03.16', 'Comisiones y otros gastos de la deuda pública externa indirecta por préstamos recibidos de proveedores de bienes y servicios externos a corto plazo  ', '4.10.03.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.04.00.00', 'Servicio de la deuda pública externa a largo plazo', '4.10.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.04.01.00', 'Servicio de la deuda pública externa a largo plazo de títulos y valores', '4.10.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.04.01.01', 'Amortización de la deuda pública externa a largo plazo de títulos y valores', '4.10.04.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.04.01.02', 'Intereses de la deuda pública externa a largo plazo de títulos y valores', '4.10.04.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.04.01.03', 'Intereses por mora y multas de la deuda pública externa a largo plazo de títulos y valores', '4.10.04.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.04.01.04', 'Comisiones y otros gastos de la deuda pública externa a largo plazo de títulos y valores', '4.10.04.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.04.01.05', 'Descuentos en colocación de títulos y valores de la deuda pública externa a largo plazo', '4.10.04.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.04.02.00', 'Servicio de la deuda pública externa por préstamos a largo plazo', '4.10.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.04.02.01', 'Amortización de la deuda pública externa por préstamos recibidos de gobiernos extranjeros a  largo plazo ', '4.10.04.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.04.02.02', 'Amortización de la deuda pública externa por préstamos  recibidos de organismos internacionales a largo plazo', '4.10.04.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.04.02.03', 'Amortización de la deuda pública externa por préstamos recibidos de instituciones financieras externas  a largo plazo', '4.10.04.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.04.02.04', 'Amortización de la deuda pública externa por préstamos recibidos de proveedores de bienes y servicios externos  a largo plazo', '4.10.04.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.04.02.05', 'Intereses de la deuda pública externa por préstamos recibidos de gobiernos extranjeros  a largo plazo ', '4.10.04.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.04.02.06', 'Intereses de la deuda pública externa por préstamos recibidos de organismos internacionales a largo plazo', '4.10.04.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.04.02.07', 'Intereses de la deuda pública externa por préstamos recibidos de instituciones financieras externas  a largo plazo', '4.10.04.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.04.02.08', 'Intereses de la deuda pública externa por préstamos recibidos de proveedores de bienes y servicios externos  a largo plazo', '4.10.04.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.04.02.09', 'Intereses por mora y multas de la deuda pública externa por préstamos  recibidos de gobiernos extranjeros  a largo plazo', '4.10.04.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.04.02.10', 'Intereses por mora y multas de la deuda pública externa por préstamos recibidos de organismos internacionales a largo plazo  ', '4.10.04.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.04.02.11', 'Intereses por mora y multas de la deuda pública externa por préstamos recibidos de instituciones financieras externas  a largo plazo ', '4.10.04.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.04.02.12', 'Intereses por mora y multas de la deuda pública externa por préstamos recibidos de proveedores de bienes y servicios externos  a largo plazo ', '4.10.04.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.04.02.13', 'Comisiones y otros gastos de la deuda pública externa por préstamos recibidos de gobiernos extranjeros  a largo plazo ', '4.10.04.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.04.02.14', 'Comisiones y otros gastos de la deuda pública externa por préstamos  recibidos de organismos internacionales a largo plazo  ', '4.10.04.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.04.02.15', 'Comisiones y otros gastos de la deuda pública externa por préstamos recibidos de instituciones financieras externas  a largo plazo ', '4.10.04.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.04.02.16', 'Comisiones y otros gastos de la deuda pública externa por préstamos  recibidos de proveedores de bienes y servicios externos  a largo plazo', '4.10.04.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.04.03.00', 'Servicio de la deuda pública externa indirecta a largo plazo de títulos y valores', '4.10.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.04.03.01', 'Amortización de la deuda pública externa indirecta a largo plazo de títulos y valores', '4.10.04.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.04.03.02', 'Intereses de la deuda pública externa indirecta a largo plazo de títulos y valores', '4.10.04.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.04.03.03', 'Intereses por mora y multas de la deuda pública externa indirecta a largo plazo de títulos y valores', '4.10.04.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.04.03.04', 'Comisiones y otros gastos de la deuda pública externa indirecta a largo plazo de títulos y valores', '4.10.04.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.04.03.05', 'Descuentos en colocación de títulos y valores de la deuda pública externa indirecta a largo plazo', '4.10.04.03.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.04.04.00', 'Servicio de la deuda pública externa indirecta por préstamos a largo plazo', '4.10.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.04.04.01', 'Amortización de la deuda pública externa indirecta por préstamos recibidos de gobiernos extranjeros a largo plazo', '4.10.04.04.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.04.04.02', 'Amortización de la deuda pública externa indirecta por préstamos recibidos de organismos internacionales a largo plazo', '4.10.04.04.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.04.04.03', 'Amortización de la deuda pública externa indirecta por préstamos recibidos de instituciones financieras externas a largo plazo', '4.10.04.04.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.04.04.04', 'Amortización de la deuda pública externa indirecta por préstamos recibidos de proveedores de bienes y servicios externos  a largo plazo', '4.10.04.04.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.04.04.05', 'Intereses de la deuda pública externa indirecta por préstamos recibidos de gobiernos extranjeros a largo plazo', '4.10.04.04.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.04.04.06', 'Intereses de la deuda pública externa indirecta por préstamos recibidos de organismos internacionales a largo plazo', '4.10.04.04.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.04.04.07', 'Intereses de la deuda pública externa indirecta por préstamos recibidos de instituciones financieras externas a largo plazo', '4.10.04.04.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.04.04.08', 'Intereses de la deuda pública externa indirecta por préstamos recibidos de proveedores de bienes y servicios externos  a largo plazo', '4.10.04.04.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.04.04.09', 'Intereses por mora y multas de la deuda pública externa indirecta por préstamos recibidos de gobiernos extranjeros a largo plazo ', '4.10.04.04.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.04.04.10', 'Intereses por mora y multas de la deuda pública externa indirecta por préstamos recibidos de organismos internacionales a largo plazo ', '4.10.04.04.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.04.04.11', 'Intereses por mora y multas de la deuda pública externa indirecta por préstamos  recibidos de instituciones financieras externas a largo plazo', '4.10.04.04.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.04.04.12', 'Intereses por mora y multas de la deuda pública externa indirecta por préstamos  recibidos de proveedores de bienes y servicios externos  a largo plazo', '4.10.04.04.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.04.04.13', 'Comisiones y otros gastos de la deuda pública externa indirecta por préstamos  recibidos de gobiernos extranjeros a largo plazo', '4.10.04.04.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.04.04.14', 'Comisiones y otros gastos de la deuda pública externa indirecta por préstamos recibidos de organismos internacionales a largo plazo ', '4.10.04.04.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.04.04.15', 'Comisiones y otros gastos de la deuda pública externa indirecta por préstamos  recibidos de instituciones financieras externas a largo plazo', '4.10.04.04.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.04.04.16', 'Comisiones y otros gastos de la deuda pública externa indirecta por préstamos  recibidos de proveedores de bienes y servicios externos  a largo plazo', '4.10.04.04.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.05.00.00', 'Reestructuración y/o refinanciamiento de la deuda publica', '4.10.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.05.01.00', 'Disminución por reestructuración y/o refinanciamiento  de la deuda interna a largo plazo, en a corto plazo', '4.10.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.05.02.00', 'Disminución por reestructuración  y/o refinanciamiento  de la deuda interna a corto plazo, en a largo plazo', '4.10.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.05.03.00', 'Disminución por reestructuración  y/o refinanciamiento  de la deuda externa a largo plazo, en a corto plazo', '4.10.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.05.04.00', 'Disminución por reestructuración y/o refinanciamiento  de la deuda externa a corto plazo, en a  largo plazo', '4.10.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.05.05.00', 'Disminución  de la deuda pública por distribuir', '4.10.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.05.05.01', 'Disminución  de la deuda pública interna por distribuir   ', '4.10.05.05.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.05.05.02', 'Disminución de la deuda pública externa por distribuir    ', '4.10.05.05.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.06.00.00', 'Servicio de la deuda pública por obligaciones de ejercicios anteriores   ', '4.10.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.06.01.00', 'Amortización de la deuda pública de obligaciones pendientes de ejercicios anteriores', '4.10.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.06.02.00', 'Intereses de la deuda pública de obligaciones pendientes de ejercicios anteriores', '4.10.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.06.03.00', 'Intereses por mora y multas de la deuda pública de obligaciones pendientes de ejercicios anteriores', '4.10.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.10.06.04.00', 'Comisiones y otros gastos de la deuda pública de obligaciones pendientes de ejercicios anteriores', '4.10.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.00.00.00', 'DISMINUCIÓN DE PASIVOS', '4.00.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.01.00.00', 'Disminución de gastos de personal por pagar', '4.11.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.01.01.00', 'Disminución de sueldos,  salarios y otras remuneraciones por pagar', '4.11.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.02.00.00', 'Disminución de aportes patronales y retenciones laborales por pagar', '4.11.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.02.01.00', 'Disminución de aportes patronales y retenciones laborales por  pagar al Instituto Venezolano de los Seguros Sociales (IVSS)', '4.11.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.02.02.00', 'Disminución de aportes patronales y retenciones laborales por  pagar al Instituto de Previsión Social del Ministerio de Educación (Ipasme)', '4.11.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.02.03.00', 'Disminución de aportes patronales y retenciones laborales por  pagar al Fondo de Jubilaciones', '4.11.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.02.04.00', 'Disminución de aportes patronales y retenciones laborales por  pagar al Fondo de Seguro de Paro Forzoso', '4.11.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.02.05.00', 'Disminución de aportes patronales y retenciones laborales por pagar al Fondo de Ahorro Obligatorio para la Vivienda (FAOV)', '4.11.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.02.06.00', 'Disminución de aportes patronales y retenciones laborales por pagar al  seguro de vida, accidentes personales, hospitalización, cirugía, maternidad (HCM) y gastos funerarios ', '4.11.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.02.07.00', 'Disminución de aportes patronales y retenciones laborales por pagar a cajas de ahorro', '4.11.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.02.08.00', 'Disminución de aportes patronales por pagar a organismos de seguridad social', '4.11.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.02.09.00', 'Disminución de retenciones laborales por pagar al Instituto Nacional de Capacitación y Educación Socialista (Inces)', '4.11.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.02.10.00', 'Disminución de retenciones laborales por  pagar por pensión alimenticia', '4.11.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.02.98.00', 'Disminución de otros aportes legales por  pagar', '4.11.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.02.99.00', 'Disminución de otras retenciones laborales por pagar', '4.11.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.03.00.00', 'Disminución de cuentas y efectos por pagar a proveedores', '4.11.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.03.01.00', 'Disminución de cuentas por pagar a proveedores a corto plazo', '4.11.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.03.02.00', 'Disminución de efectos por pagar a  proveedores a corto plazo', '4.11.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.03.03.00', 'Disminución de cuentas por pagar a proveedores a mediano y largo plazo ', '4.11.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.03.04.00', 'Disminución de efectos por pagar a proveedores a mediano y largo plazo', '4.11.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.04.00.00', 'Disminución de cuentas y efectos por pagar a contratistas', '4.11.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.04.01.00', 'Disminución de cuentas por pagar a contratistas a corto plazo', '4.11.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.04.04.00', 'Disminución de efectos por pagar a  contratistas a mediano y largo plazo', '4.11.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.05.00.00', 'Disminución de intereses por pagar    ', '4.11.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.05.01.00', 'Disminución de intereses internos por pagar', '4.11.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.05.02.00', 'Disminución de intereses externos por pagar', '4.11.05.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.06.00.00', 'Disminución de otras cuentas y efectos por pagar a corto plazo', '4.11.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.06.01.00', 'Disminución de obligaciones de ejercicios anteriores             ', '4.11.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.06.02.00', 'Disminución de otras cuentas por pagar a corto plazo', '4.11.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.06.03.00', 'Disminución de otros efectos por pagar a corto plazo', '4.11.06.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.07.00.00', 'Disminución de pasivos diferidos', '4.11.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.07.01.00', 'Disminución de pasivos diferidos a corto plazo', '4.11.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.07.01.01', 'Disminución de rentas diferidas por recaudar a corto plazo', '4.11.07.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.07.02.00', 'Disminución de pasivos diferidos a mediano y largo plazo', '4.11.07.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.07.02.01', 'Disminución del rescate de certificados de reintegro tributario ', '4.11.07.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.07.02.02', 'Disminución del rescate de bonos de exportación', '4.11.07.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.07.02.03', 'Disminución del rescate de bonos en dación de pagos', '4.11.07.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.08.00.00', 'Disminución de provisiones y reservas técnicas', '4.11.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.08.01.00', 'Disminución de provisiones ', '4.11.08.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.08.01.01', 'Disminución de provisiones para cuentas incobrables', '4.11.08.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.08.01.02', 'Disminución de provisiones para despidos', '4.11.08.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.08.01.03', 'Disminución de provisiones para pérdidas en el inventario', '4.11.08.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.08.01.04', 'Disminución de provisiones para beneficios sociales', '4.11.08.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.08.01.99', 'Disminución de otras  provisiones ', '4.11.08.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.08.02.00', 'Disminución de reservas técnicas', '4.11.08.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.09.00.00', 'Disminución de fondos de terceros', '4.11.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.09.01.00', 'Disminución de depósitos recibidos en garantía', '4.11.09.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.09.99.00', 'Disminución de otros fondos de terceros', '4.11.09.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.10.00.00', 'Disminución de depósitos de instituciones financieras ', '4.11.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.10.01.00', 'Disminución de depósitos a la vista', '4.11.10.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.10.01.01', 'Disminución de depósitos  de terceros a la vista de organismos del sector público', '4.11.10.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.10.01.02', 'Disminución de depósitos de terceros a la vista de personas naturales y jurídicas del sector privado', '4.11.10.01.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.10.02.00', 'Disminución de depósitos a plazo fijo', '4.11.10.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.10.02.01', 'Disminución de depósitos a plazo fijo de organismos del sector público', '4.11.10.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.10.02.02', 'Disminución de depósitos a plazo fijo de personas naturales y jurídicas del sector privado', '4.11.10.02.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.11.00.00', 'Obligaciones de ejercicios anteriores', '4.11.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.11.01.00', 'Devoluciones de cobros indebidos', '4.11.11.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.11.02.00', 'Devoluciones y reintegros diversos', '4.11.11.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.11.03.00', 'Indemnizaciones diversas', '4.11.11.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.11.04.00', 'Compromisos pendientes de ejercicios anteriores', '4.11.11.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.11.05.00', 'Prestaciones sociales originadas por la aplicación de la Ley Orgánica del Trabajo, los Trabajadores y las Trabajadoras', '4.11.11.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.98.00.00', 'Disminución de otros pasivos a corto plazo', '4.11.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.98.01.00', 'Disminución de otros pasivos a corto plazo', '4.11.98.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.99.00.00', 'Disminución de otros pasivos a mediano y largo plazo', '4.11.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.11.99.01.00', 'Disminución de otros pasivos a mediano y largo plazo', '4.11.99.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.12.00.00.00', 'DISMINUCIÓN DEL PATRIMONIO', '4.00.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.12.01.00.00', 'Disminución del capital ', '4.12.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.12.01.01.00', 'Disminución del capital fiscal e institucional', '4.12.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.12.01.02.00', 'Disminución de aportes por capitalizar', '4.12.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.12.01.03.00', 'Disminución de dividendos a distribuir', '4.12.01.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.12.02.00.00', 'Disminución de reservas ', '4.12.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.12.02.01.00', 'Disminución de reservas ', '4.12.02.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.12.03.00.00', 'Ajuste por inflación', '4.12.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.12.03.01.00', 'Ajuste por inflación', '4.12.03.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.12.04.00.00', 'Disminución de resultados', '4.12.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.12.04.01.00', 'Disminución de resultados acumulados ', '4.12.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.12.04.02.00', 'Disminución de resultados del ejercicio ', '4.12.04.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.98.00.00.00', 'RECTIFICACIONES AL PRESUPUESTO', '4.00.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.98.01.00.00', 'Rectificaciones al presupuesto', '4.98.00.00.00');
INSERT INTO z_partidas_presupuestarias VALUES ('4.98.01.01.00', 'Rectificaciones al presupuesto', '4.98.01.00.00');


--
-- TOC entry 1911 (class 2606 OID 70999)
-- Dependencies: 1620 1620
-- Name: clasificador_presupuestario_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY z_partidas_presupuestarias
    ADD CONSTRAINT clasificador_presupuestario_pkey PRIMARY KEY (id_partida);


--
-- TOC entry 1909 (class 2606 OID 42861)
-- Dependencies: 1608 1608
-- Name: p_actividades_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY p_actividades
    ADD CONSTRAINT p_actividades_pkey PRIMARY KEY (id_actividad);


--
-- TOC entry 1915 (class 2606 OID 79213)
-- Dependencies: 1624 1624
-- Name: p_fuentes_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY p_fuentes
    ADD CONSTRAINT p_fuentes_pkey PRIMARY KEY (id_fuente);


--
-- TOC entry 1913 (class 2606 OID 71025)
-- Dependencies: 1622 1622
-- Name: p_presupuesto_actividad_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY p_presupuesto_actividad
    ADD CONSTRAINT p_presupuesto_actividad_pkey PRIMARY KEY (id_presupuesto);


--
-- TOC entry 1916 (class 2606 OID 42862)
-- Dependencies: 1605 1608
-- Name: p_actividades_id_accion_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY p_actividades
    ADD CONSTRAINT p_actividades_id_accion_fkey FOREIGN KEY (id_accion) REFERENCES p_acciones(id_accion) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 1918 (class 2606 OID 79226)
-- Dependencies: 1914 1624 1608
-- Name: p_actividades_id_fuente_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY p_actividades
    ADD CONSTRAINT p_actividades_id_fuente_fkey FOREIGN KEY (id_fuente) REFERENCES p_fuentes(id_fuente) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 1917 (class 2606 OID 42867)
-- Dependencies: 1608 1587
-- Name: p_actividades_id_responsable_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY p_actividades
    ADD CONSTRAINT p_actividades_id_responsable_fkey FOREIGN KEY (id_responsable) REFERENCES a_usuarios(id_usuario) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 1919 (class 2606 OID 71046)
-- Dependencies: 1908 1608 1622
-- Name: p_presupuesto_actividad_id_actividad_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY p_presupuesto_actividad
    ADD CONSTRAINT p_presupuesto_actividad_id_actividad_fkey FOREIGN KEY (id_actividad) REFERENCES p_actividades(id_actividad) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 1920 (class 2606 OID 71051)
-- Dependencies: 1622 1620 1910
-- Name: p_presupuesto_actividad_id_partida_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY p_presupuesto_actividad
    ADD CONSTRAINT p_presupuesto_actividad_id_partida_fkey FOREIGN KEY (id_partida) REFERENCES z_partidas_presupuestarias(id_partida) ON UPDATE CASCADE ON DELETE RESTRICT;


-- Completed on 2013-07-28 23:17:08

--
-- PostgreSQL database dump complete
--

