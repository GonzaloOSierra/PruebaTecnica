let context;

const mysql = require('mysql2/promise');

const db = mysql.createPool({
  host: process.env.DB_HOST || 'db',
  user: process.env.DB_USER || 'scraper',
  password: process.env.DB_PASSWORD || 'scraperpass',
  database: process.env.DB_NAME || 'scraping',
  waitForConnections: true,
  connectionLimit: 10
});

const { chromium } = require('playwright');
const cheerio = require('cheerio');

// =======================
// SCROLL HUMANO
// =======================
async function humanScroll(page, maxScrolls = 35) {
  for (let i = 0; i < maxScrolls; i++) {
    const distance = 200 + Math.floor(Math.random() * 300); // 200–500px
    await page.mouse.wheel(0, distance);

    const pause = 600 + Math.floor(Math.random() * 1200); // 0.6s–1.8s
    await page.waitForTimeout(pause);

    const reachedBottom = await page.evaluate(() => {
      return (
        window.innerHeight + window.scrollY >=
        document.body.scrollHeight - 100
      );
    });

    if (reachedBottom) break;
  }
}

async function run() {
  try {
    console.log('Abriendo navegador (sesión persistente)...');

    // 🔥 SESIÓN PERSISTENTE
    context = await chromium.launchPersistentContext('./ml-session', {
      headless: true,
      slowMo: 40,
      viewport: { width: 1366, height: 768 },
      locale: 'es-AR',
      userAgent:
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36'
    });

    const page = await context.newPage();

    console.log('Entrando a MercadoLibre (OFERTAS)...');
    await page.goto('https://www.mercadolibre.com.ar/ofertas', {
      waitUntil: 'domcontentloaded',
      timeout: 90000
    });

    // dejar que hidrate JS
    await page.waitForTimeout(6000);

    // aceptar cookies si aparece
    try {
      await page.click('button:has-text("Aceptar")', { timeout: 5000 });
      console.log('Cookies aceptadas');
    } catch {
      console.log('No apareció banner de cookies');
    }

    // señales humanas mínimas
    await page.mouse.move(300, 400);
    await page.waitForTimeout(900);
    await page.mouse.move(600, 500);
    await page.waitForTimeout(1200);

    console.log('Scrolleando como humano...');
    await humanScroll(page, 40);
    await page.waitForTimeout(3000);

    // =======================
    // DEBUG REAL
    // =======================
    const debug = await page.evaluate(() => ({
      polyCard: document.querySelectorAll('.poly-card').length,
      anchors: document.querySelectorAll('a').length,
      images: document.querySelectorAll('img').length,
      scripts: document.scripts.length
    }));

    console.log('DEBUG DOM:', debug);

    // =======================
    // HTML + CHEERIO
    // =======================
    const html = await page.content();
    const $ = cheerio.load(html);

    const productos = [];

    $('.poly-card').each((_, el) => {
      const titulo = $(el)
        .find('.poly-component__title')
        .text()
        .trim();

      const precioActual = $(el)
        .find('.poly-price__current .andes-money-amount__fraction')
        .first()
        .text()
        .trim();

      const precioAnterior = $(el)
        .find('.andes-money-amount--previous .andes-money-amount__fraction')
        .first()
        .text()
        .trim();

      const link = $(el)
        .find('.poly-component__title')
        .attr('href');

      const imagen =
        $(el).find('img').attr('data-src') ||
        $(el).find('img').attr('src');

      if (titulo) {
        productos.push({
          titulo,
          precioActual,
          precioAnterior,
          link,
          imagen
        });
      }
    });

    console.log('Productos capturados:', productos.length);
    console.log(productos.slice(0, 5));

    console.log('Guardando productos en la base de datos...');

    for (const producto of productos) {
      try {
        await guardarProducto(producto);
      } catch (err) {
        console.error('Error guardando producto:', producto.titulo, err.message);
      }
    }

    console.log('Productos guardados correctamente');
    
    console.log('Script terminado correctamente');
  } catch (err) {
    console.error('❌ Error general:', err);
  } finally {
    console.log('Cerrando recursos...');

    if (context) await context.close();
    await db.end();

    console.log('Script finalizado');
    process.exit(0);
  }
}

function parsePrecio(valor) {
  if (!valor) return null;
  return Number(valor.replace(/\./g, '').replace(',', '.'));
}

async function guardarProducto(producto) {
  const {
    titulo,
    precioActual,
    precioAnterior,
    link,
    imagen
  } = producto;

  // 1️⃣ buscar producto
  const [rows] = await db.query(
    'SELECT id FROM productos WHERE titulo = ?',
    [titulo]
  );

  let productoId;

  if (rows.length === 0) {
    const [result] = await db.query(
      `INSERT INTO productos (titulo, link, imagen, marca_id)
      VALUES (?, ?, ?, NULL)`,
      [titulo, link, imagen]
    );
    productoId = result.insertId;
  } else {
    productoId = rows[0].id;
  }

  // 3️⃣ insertar registro
  await db.query(
    `INSERT INTO producto_registros
    (producto_id, precio_actual, precio_anterior)
    VALUES (?, ?, ?)`,
    [
      productoId,
      parsePrecio(precioActual),
      parsePrecio(precioAnterior)
    ]
  );


}

run();
