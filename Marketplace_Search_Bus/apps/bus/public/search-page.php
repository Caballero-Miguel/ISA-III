<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Marketplace Search Bus</title>
    <link rel="stylesheet" href="/assets/styles.css">
</head>
<body>
<header class="topbar">
    <div>
        <p class="eyebrow">Universidad Tecnológica de Panamá · ISA III</p>
        <h1>Marketplace Search Bus</h1>
        <p>Busca en tres servicios independientes con una sola consulta.</p>
    </div>
    <a class="nav-link" href="/admin">Administración</a>
</header>

<main>
    <section class="panel search-panel" aria-labelledby="search-title">
        <div class="section-heading">
            <div>
                <p class="eyebrow">Bus de Web Services</p>
                <h2 id="search-title">Búsqueda unificada</h2>
            </div>
            <span class="live-chip"><span aria-hidden="true"></span> Servicios locales</span>
        </div>

        <form id="search-form">
            <div class="search-row">
                <label class="search-box">
                    <span class="sr-only">Producto o palabra clave</span>
                    <input id="q" name="q" type="search" maxlength="120" placeholder="Laptop, monitor, mouse…">
                </label>
                <button id="search-button" class="button primary" type="submit">Buscar productos</button>
            </div>
            <div class="filter-grid">
                <label>Categoría
                    <select name="category">
                        <option value="">Todas</option>
                        <option value="computers">Computadoras</option>
                        <option value="phones">Teléfonos</option>
                        <option value="audio">Audio</option>
                        <option value="gaming">Gaming</option>
                        <option value="accessories">Accesorios</option>
                        <option value="home-office">Oficina en casa</option>
                    </select>
                </label>
                <label>Precio mínimo
                    <input name="min_price" type="number" min="0" max="100000" step="0.01" placeholder="0.00">
                </label>
                <label>Precio máximo
                    <input name="max_price" type="number" min="0" max="100000" step="0.01" placeholder="900.00">
                </label>
                <label>Proveedor
                    <select name="provider">
                        <option value="all">Todos</option>
                        <option value="alpha">Alpha</option>
                        <option value="beta">Beta</option>
                        <option value="gamma">Gamma</option>
                    </select>
                </label>
                <label>Orden
                    <select name="sort">
                        <option value="default">Predeterminado</option>
                        <option value="price_asc">Precio: menor primero</option>
                        <option value="price_desc">Precio: mayor primero</option>
                        <option value="name_asc">Nombre A–Z</option>
                    </select>
                </label>
                <label>Stock mínimo
                    <input name="min_stock" type="number" min="0" max="100000" step="1" placeholder="Cualquiera">
                </label>
            </div>
        </form>
        <div id="message" class="message" role="status" aria-live="polite" hidden></div>
    </section>

    <section id="search-meta" class="panel meta-panel" hidden aria-label="Metadatos de búsqueda">
        <div><span>ID de búsqueda</span><strong id="search-id">—</strong></div>
        <div><span>Expira en</span><strong id="countdown">—</strong></div>
        <div><span>Resultados</span><strong id="result-count">0</strong></div>
        <div class="provider-statuses" id="provider-statuses"></div>
    </section>

    <section aria-labelledby="results-title">
        <div class="results-heading">
            <div>
                <p class="eyebrow">Catálogo agregado</p>
                <h2 id="results-title">Resultados</h2>
            </div>
        </div>
        <div id="results" class="product-grid">
            <div class="empty-state">
                <span>⌕</span>
                <h3>Realiza una búsqueda</h3>
                <p>El Bus consultará Alpha, Beta y Gamma por HTTP y normalizará sus respuestas.</p>
            </div>
        </div>
    </section>
</main>

<footer>Proyecto académico · Datos ficticios · REST/JSON + PHP PDO</footer>
<script src="/assets/search.js" defer></script>
</body>
</html>

