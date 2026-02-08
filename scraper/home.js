let context;
const ahoraARG = new Date().toLocaleString('es-AR', {
  timeZone: 'America/Argentina/Buenos_Aires'
});
const fs = require('fs');

const mysql = require('mysql2/promise');

const db = mysql.createPool({
  host: process.env.DB_HOST,
  user: process.env.DB_USER,
  password: process.env.DB_PASSWORD,
  database: process.env.DB_NAME,
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
    console.log('Hora scrapeo:', ahoraARG);

    // Sesion persistente
    context = await chromium.launchPersistentContext('./ml-session', {
      headless: true,
      slowMo: 40,
      viewport: { width: 1366, height: 768 },
      locale: 'es-AR',
      userAgent:
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36'
    });

    const page = await context.newPage();

    console.log('Entrando a MercadoLibre (HOME)...');
    await page.goto('https://www.mercadolibre.com.ar', {
      waitUntil: 'domcontentloaded',
      timeout: 90000
    });

    await page.waitForTimeout(6000);

    // aceptar cookies si aparece
    try {
      await page.click('button:has-text("Aceptar")', { timeout: 5000 });
      console.log('Cookies aceptadas');
    } catch {
      console.log('No apareció banner de cookies');
    }

    // Moverse como humano
    await page.mouse.move(300, 400);
    await page.waitForTimeout(900);
    await page.mouse.move(600, 500);
    await page.waitForTimeout(1200);

    console.log('Scrolleando como humano...');
    await humanScroll(page, 40);
    await page.waitForTimeout(3000);
    await page.waitForSelector('.video-player-wrapper', { timeout: 10000 });
await page.waitForSelector('.video-player-wrapper', { timeout: 10000 }).catch(() => {
  console.log('No se encontraron mplays en el DOM');
});

const mplays = await page.evaluate(() => {
  const data = [];
  document.querySelectorAll('.video-player-wrapper').forEach(el => {
    const img = el.querySelector('img');
    if (img) {
      data.push({
        titulo: img.alt || null,
        link: 'https://play.mercadolibre.com.ar',
        imagen: img.src || img.dataset.src || null,
        tipo: 'mplay'
      });
    }
  });
  return data;
});

    ///////// DEBUG REAL //////////////////
    const debug = await page.evaluate(() => ({
      slides: document.querySelectorAll('.andes-carousel-snapped__slide').length,
      slidesWithLink: document.querySelectorAll('.andes-carousel-snapped__slide a').length,
      slidesWithImg: document.querySelectorAll('.andes-carousel-snapped__slide img').length,
      totalScripts: document.scripts.length
    }));

    console.log('DEBUG DOM:', debug);

    //////////// HTML + CHEERIO ////////////////

    const html = await page.content();
    const $ = cheerio.load(html);

fs.writeFileSync(
  './debug-mercadolibre.html',
  html,
  { encoding: 'utf-8' }
);

console.log('HTML guardado en debug-mercadolibre.html');



    //////////// INSERCION DE BANNERS //////////////

    const banners = [];
    console.log('Slides encontrados:', $('.andes-carousel-snapped__slide').length);


    $('.andes-carousel-snapped__exhibitor-wrapper')
      .first()
      .find('.andes-carousel-snapped__slide')
      .each((i, el) => {

        if (i >= 7) return false;
          
      const titulo =
        $(el).find('img').attr('alt') ||
        $(el).find('.title').text().trim() ||
        null;

      const link = 
        $(el).find('a').attr('href') || null;

      const imagen =
        $(el).find('img').attr('data-src') ||
        $(el).find('img').attr('src') ||
        null;

      const tipo = 'banner';

      banners.push({
        titulo,
        link,
        imagen,
        tipo
      });
    });

      console.log('Banners capturados:', banners.length);
      console.log(banners.slice(0, 5));

      console.log('Guardando banners en la base de datos...');

      for (const banner of banners) {
        try {
          await guardarBanner(banner);
        } catch (err) {
          console.error('Error guardando banner:', banner.titulo, err.message);
        }
      }

      console.log('Banners guardados correctamente');
      

    //////////// INSERCION DE PARTNER SUBSCRIPTION //////////////


    const subs = [];
    console.log('Slides encontrados:', $('.partners-subscriptions__slide').length);


    $('.partners-subscriptions__slide').each((_, el) => {

      const titulo =
        $(el).find('img').attr('alt') ||
        $(el).find('.title').text().trim() ||
        null;

      const link = 
        $(el).find('a').attr('href') || null;

      const imagen =
        $(el).find('img').attr('data-src') ||
        $(el).find('img').attr('src') ||
        null;

      const tipo = 'subscripciones';

      subs.push({
        titulo,
        link,
        imagen,
        tipo
      });
    });

      console.log('Subscripciones capturados:', subs.length);
      console.log(subs.slice(0, 5));

      console.log('Guardando Subscripciones en la base de datos...');

      for (const sub of subs) {
        try {
          await guardarBanner(sub);
        } catch (err) {
          console.error('Error guardando Subscripciones:', sub.titulo, err.message);
        }
      }

      console.log('Subscripciones guardados correctamente');
      

      console.log('Script terminado correctamente');

  } catch (err) {
    console.error('Error general:', err);
  } finally {
    console.log('Cerrando recursos...');

    if (context) await context.close();
    await db.end();

    console.log('Script finalizado');
    process.exit(0);
  }
}

//////////// BANNERS //////////

async function guardarBanner(banner) {
  const {
    titulo,
    link,
    imagen,
    tipo
  } = banner;

  // buscar producto
  const [rows] = await db.query(
    'SELECT id FROM banner WHERE imagen = ?',
    [imagen]
  );

  let id_banner;

  if (rows.length === 0) {
    const [result] = await db.query(
      `INSERT INTO banner (titulo, link, imagen, tipo)
      VALUES (?, ?, ?, ?)`,
      [titulo, link, imagen, tipo]
    );
    id_banner = result.insertId;
  } else {
    id_banner = rows[0].id;
  }

}

run();
