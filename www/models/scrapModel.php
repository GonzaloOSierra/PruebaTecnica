<?php
require_once __DIR__ . '/../config/model.php';
class ScrapModel extends Model {

    private $id;
    private $titulo;
    private $link;
    private $imagen;
    private $creado_en;
    private $id_reg;
    private $producto_id;
    private $precio_actual;
    private $precio_anterior;
    private $registrado_en;
    private $id_mark;
    private $m_name;

    public function __construct() {
        parent::__construct();
        $this->id = null;
        $this->titulo = '';
        $this->link = '';
        $this->imagen = '';
        $this->creado_en = null;
        $this->id_reg = null;
        $this->producto_id = null;
        $this->precio_actual = null;
        $this->precio_anterior = null;
        $this->registrado_en = '';
        $this->id_mark = null;
        $this->m_name = '';
    }


    public function obtenerRegistros() {
        $items = [];
        try {
        $query = $this->query('
            SELECT 
                        pr.id, 
                        pr.precio_actual,
                        pr.precio_anterior,
                        pr.registrado_en,
                        p.titulo AS titulo,
                        p.link,
                        p.imagen,
                        p.creado_en
                    FROM producto_registros pr
                    LEFT JOIN productos p ON p.id = pr.producto_id
                    ORDER BY pr.registrado_en DESC
            ');

            while ($o = $query->fetch(PDO::FETCH_ASSOC)) {
                $item = new ScrapModel();
                $item->from($o); 
                array_push($items, $item);
            }

            return $items;
        } catch (PDOException $e) {
            error_log('SCRAPMODEL::obtenerRegistros -> ' . $e->getMessage());
            return [];
        }
    }

    public function obtenerBanners() {
        $items = [];
        try {
        $query = $this->query('
                    SELECT 
                        titulo,
                        link,
                        imagen,
                        created_at as creado_en
                    FROM banner
                    WHERE tipo = "banner"
                    ORDER BY created_at DESC
            ');

            while ($o = $query->fetch(PDO::FETCH_ASSOC)) {
                $item = new ScrapModel();
                $item->from($o); 
                array_push($items, $item);
            }

            return $items;
        } catch (PDOException $e) {
            error_log('SCRAPMODEL::obtenerBanners -> ' . $e->getMessage());
            return [];
        }
    }

    public function obtenerSubs() {
        $items = [];
        try {
        $query = $this->query('
                    SELECT 
                        titulo,
                        link,
                        imagen,
                        created_at as creado_en
                    FROM banner
                    WHERE tipo = "subscripciones"
                    ORDER BY created_at DESC
            ');

            while ($o = $query->fetch(PDO::FETCH_ASSOC)) {
                $item = new ScrapModel();
                $item->from($o); 
                array_push($items, $item);
            }

            return $items;
        } catch (PDOException $e) {
            error_log('SCRAPMODEL::obtenerSubs -> ' . $e->getMessage());
            return [];
        }
    }

    ////////////// DASHBOARD /////////////////

    public function obtPorcTotal() {
        $items = [];
        try {
            $query = $this->query("
                SELECT 
                    CASE 
                        WHEN p.marca_id IS NULL OR p.marca_id = 0 
                            THEN 'Sin marca'
                        ELSE m.nombre
                    END AS marca,
                    COUNT(*) AS cantidad,
                    ROUND(
                        COUNT(*) * 100 / (SELECT COUNT(*) FROM productos),
                        2
                    ) AS porcentaje
                FROM productos p
                LEFT JOIN marcas m ON m.id = p.marca_id
                GROUP BY marca
                ORDER BY porcentaje DESC;

            ");

            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('SCRAPMODEL::obtPorcTotal -> ' . $e->getMessage());
            return [];
        }
    }

    public function productosPorRango($desde, $hasta) {
        try {
            $sql = '
                SELECT
                    p.id,
                    p.titulo,
                    COUNT(DISTINCT DATE(pr.registrado_en)) AS dias_aparecido
                FROM producto_registros pr
                INNER JOIN productos p ON p.id = pr.producto_id
                WHERE DATE(pr.registrado_en) BETWEEN :desde AND :hasta
                GROUP BY p.id, p.titulo
                ORDER BY dias_aparecido DESC
            ';

            $stmt = $this->prepare($sql);
            $stmt->bindValue(':desde', $desde);
            $stmt->bindValue(':hasta', $hasta);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log('SCRAPMODEL::productosPorRango -> ' . $e->getMessage());
            return [];
        }
    }

    public function newPM($mes, $anio) {
        try {
            $inicio = "$anio-$mes-01";
            $fin = date('Y-m-t', strtotime($inicio));

            $sql = "
                SELECT
                    SUM(CASE 
                        WHEN primera_aparicion BETWEEN :inicio1 AND :fin 
                        THEN 1 ELSE 0 
                    END) AS nuevos,

                    SUM(CASE 
                        WHEN primera_aparicion < :inicio2 
                        THEN 1 ELSE 0 
                    END) AS repetidos
                FROM (
                    SELECT 
                        producto_id, 
                        DATE(MIN(registrado_en)) AS primera_aparicion
                    FROM producto_registros
                    GROUP BY producto_id
                ) t
            ";

            $stmt = $this->prepare($sql);
            $stmt->bindValue(':inicio1', $inicio);
            $stmt->bindValue(':inicio2', $inicio);
            $stmt->bindValue(':fin', $fin);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log('SCRAPMODEL::newPM -> ' . $e->getMessage());
            return ['nuevos' => 0, 'repetidos' => 0];
        }
    }



    public function prodNewMes($mes, $anio) {
        try {
            $inicio = "$anio-$mes-01";
            $fin = date('Y-m-t', strtotime($inicio));

            $sql = "
                SELECT
                    p.id,
                    p.titulo,
                    MIN(DATE(pr.registrado_en)) AS primera_aparicion
                FROM producto_registros pr
                JOIN productos p ON p.id = pr.producto_id
                GROUP BY p.id, p.titulo
                HAVING primera_aparicion BETWEEN :inicio AND :fin
                ORDER BY primera_aparicion DESC
            ";

            $stmt = $this->prepare($sql);
            $stmt->bindValue(':inicio', $inicio);
            $stmt->bindValue(':fin', $fin);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log('SCRAPMODEL::prodNewMes -> ' . $e->getMessage());
            return [];
        }
    }


    ////////////// TITULOS /////////////////

    public function obtTitles() {
        $items = [];
        try {
            $query = $this->query('
                SELECT 
                    p.id as id,
                    p.titulo,
                    m.nombre AS m_name
                FROM productos p
                LEFT JOIN marcas m ON m.id = p.marca_id
            ');

            while ($o = $query->fetch(PDO::FETCH_ASSOC)) {
                $item = new ScrapModel();
                $item->from($o);
                array_push($items, $item);
            }

            return $items;
        } catch (PDOException $e) {
            error_log('SCRAPMODEL::obtTitles -> ' . $e->getMessage());
            return [];
        }
    }


    public function colocarMarcas($id, $mark) {
        try {
            $sql = '
                UPDATE productos
                SET marca_id = :marca_id
                WHERE (marca_id IS NULL OR marca_id = 0)
                AND LOWER(titulo) LIKE :mark
            ';

            $stmt = $this->prepare($sql);
            $stmt->bindValue(':marca_id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':mark', '%' . mb_strtolower($mark) . '%', PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->rowCount();

        } catch (PDOException $e) {
            error_log('SCRAPMODEL::colocarMarcas -> ' . $e->getMessage());
            return 0;
        }
    }

    public function topDiez() {
        try {
            $sql = "
                SELECT
                    p.id,
                    p.titulo,
                    COUNT(DISTINCT DATE(pr.registrado_en)) AS dias_aparecido
                FROM producto_registros pr
                JOIN productos p ON p.id = pr.producto_id
                GROUP BY p.id, p.titulo
                ORDER BY dias_aparecido DESC
                LIMIT 10;
            ";

            $stmt = $this->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log('SCRAPMODEL::top10ProductosMasSolicitados -> ' . $e->getMessage());
            return [];
        }
    }


    ////////////// MARCAS /////////////////

    public function obtenerMarks() {
        $items = [];
        try {
            $query = $this->query('
                SELECT 
                    m.id as id_mark,
                    m.nombre as m_name
                FROM marcas m
            ');

            while ($o = $query->fetch(PDO::FETCH_ASSOC)) {
                $item = new ScrapModel();
                $item->from($o);
                array_push($items, $item);
            }

            return $items;
        } catch (PDOException $e) {
            error_log('SCRAPMODEL::obtenerMarks -> ' . $e->getMessage());
            return [];
        }
    }

    public function addMark($name) {
        try {
            $sql = 'INSERT INTO marcas (nombre) VALUES (:nombre)';
            $stmt = $this->prepare($sql);
            $stmt->bindValue(':nombre', trim($name), PDO::PARAM_STR);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log('SCRAPMODEL::addMark -> ' . $e->getMessage());
            return false;
        }
    }

    public function getMark($id) {
        try {
            $sql = 'SELECT id AS id_mark, nombre AS m_name 
                    FROM marcas 
                    WHERE id = :id';

            $stmt = $this->prepare($sql);
            $stmt->bindValue(':id', trim($id), PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log('SCRAPMODEL::getMark -> ' . $e->getMessage());
            return null;
        }
    }

    public function actMark($id, $name) {
        try {
            $sql = 'UPDATE marcas SET nombre = :nombre WHERE id = :id';
            $stmt = $this->prepare($sql);
            $stmt->bindValue(':nombre', trim($name), PDO::PARAM_STR);
            $stmt->bindValue(':id', trim($id), PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log('SCRAPMODEL::addMark -> ' . $e->getMessage());
            return false;
        }
    }


    public function from($array) {
        $this->id = $array['id'] ?? null;
        $this->titulo = $array['titulo'] ?? '';
        $this->link = $array['link'] ?? '';
        $this->imagen = $array['imagen'] ?? null;
        $this->creado_en = $array['creado_en'] ?? null;
        $this->id_reg = $array['id_reg'] ?? '';
        $this->producto_id = $array['producto_id'] ?? '';
        $this->precio_actual = $array['precio_actual'] ?? '';
        $this->precio_anterior = $array['precio_anterior'] ?? '';
        $this->registrado_en = $array['registrado_en'] ?? null;
        $this->id_mark = $array['id_mark'] ?? null;
        $this->m_name = $array['m_name'] ?? '';
    }


    // Setters
    public function setId($id) { $this->id = $id; }
    public function setTitle($titulo) { $this->titulo = $titulo; }
    public function setLink($link) { $this->link = $link; }
    public function setImage($imagen) { $this->imagen = $imagen; }
    public function setCreate_At($creado_en) { $this->creado_en = $creado_en; }
    public function setId_Reg($id_reg) { $this->id_reg = $id_reg; }
    public function setProduct_Id($producto_id) { $this->producto_id = $producto_id; }
    public function setActual_Price($precio_actual) { $this->precio_actual = $precio_actual; }
    public function setLast_Price($precio_anterior) { $this->precio_anterior = $precio_anterior; }
    public function setRegistered_At($registrado_en) { $this->registrado_en = $registrado_en; }
    public function setId_Mark($id_mark) { $this->id_mark = $id_mark; }
    public function setM_Name($m_name) { $this->m_name = $m_name; }

    // Getters
    public function getId() { return $this->id; }
    public function getTitle() { return $this->titulo; }
    public function getLink() { return $this->link; }
    public function getImage() { return $this->imagen; }
    public function getCreate_At() { return $this->creado_en; }
    public function getId_Reg() { return $this->id_reg; }
    public function getProduct_Id() { return $this->producto_id; }
    public function getActual_Price() { return $this->precio_actual; }
    public function getLast_Price() { return $this->precio_anterior; }
    public function getRegistered_At() { return $this->registrado_en; }
    public function getId_Mark() { return $this->id_mark; }
    public function getM_Name() { return $this->m_name; }

}
