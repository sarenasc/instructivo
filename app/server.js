const express = require('express');
const sql = require('mssql');
const cors = require('cors');

const app = express();
app.use(cors());
app.use(express.json());

const config = {
    user: 'sa',
    password: 'Robin@2021',
    server: '192.168.19.4',
    database: 'SistGestion',
    options: {
        encrypt: false,
        trustServerCertificate: true,
    }
};

// ✅ Obtener lista de exportadoras
app.get('/api/exportadoras', async (req, res) => {
    try {
        const pool = await sql.connect(config);
        const result = await pool.request().query(`
            SELECT DISTINCT exp.id, exp.Nombre_Exportadora
            FROM inst_cab_instructivo cab
            INNER JOIN inst_exportadora exp ON exp.id = cab.id_exportadora
        `);
        res.json(result.recordset);
    } catch (err) {
        res.status(500).send(err.message);
    }
});

// ✅ Obtener instructivos por exportadora
app.get('/api/instructivos/:id_exportadora', async (req, res) => {
    try {
        const pool = await sql.connect(config);
        const result = await pool.request()
            .input('id_exportadora', sql.Int, req.params.id_exportadora)
            .query(`
                SELECT DISTINCT id_instructivo
                FROM inst_cab_instructivo
                WHERE id_exportadora = @id_exportadora
            `);
        res.json(result.recordset);
    } catch (err) {
        res.status(500).send(err.message);
    }
});

// ✅ Obtener versiones por instructivo
app.get('/api/versiones/:id_instructivo', async (req, res) => {
    try {
        const pool = await sql.connect(config);
        const result = await pool.request()
            .input('id_instructivo', sql.Int, req.params.id_instructivo)
            .query(`
                SELECT DISTINCT version
                FROM inst_detalle_instructivo
                WHERE id_cab_instructivo = @id_instructivo
            `);
        res.json(result.recordset);
    } catch (err) {
        res.status(500).send(err.message);
    }
});

// ✅ Consulta principal: detalle por instructivo y versión
// ✅ Consulta principal modificada: obtiene automáticamente la última versión
app.get('/api/detalle', async (req, res) => {
    const { id_instructivo } = req.query;

    try {
        const pool = await sql.connect(config);

        // Obtener la última versión del instructivo
        const versionResult = await pool.request()
            .input('id_instructivo', sql.Int, id_instructivo)
            .query(`
                SELECT MAX(version) AS ultimaVersion
                FROM inst_detalle_instructivo
                WHERE id_cab_instructivo = @id_instructivo
            `);

        const ultimaVersion = versionResult.recordset[0]?.ultimaVersion;

        if (!ultimaVersion) {
            return res.status(404).json({ message: 'No se encontraron versiones para este instructivo.' });
        }

        // Obtener los datos del instructivo con la última versión
        const result = await pool.request()
            .input('id_instructivo', sql.Int, id_instructivo)
            .input('version', sql.Int, ultimaVersion)
            .query(`
                SELECT
                    exp.Nombre_Exportadora,
                    i.id_cab_instructivo AS 'Numero Instructivo',
                    i.numero_pedido,
                    esp.especie,
                    i.var_etiquetada,
                    e.codigo_emb AS embalaje,
                    e.Descripcion_Embalaje, 
                    e.Peso_Embalaje,
                    et.Nombre_etiqueta,
                    d.nombre_destino,
                    pl.plu,
                    CONCAT(ca.cod_categoria,' - ',ca.nombre_categoria) AS categoria,
                    p.Descrip_pallet,
                    CONCAT(ap.altura,'/',ap.cajas) as Altura,
                    i.observacion AS ObservacionDetalle,
                    cab.observacion AS ObservacionCabecera,
                    c.nombre_calibre,
                    i.cantidad_pedido,
                    @version AS version
                FROM inst_detalle_instructivo i
                INNER JOIN inst_cab_instructivo cab ON cab.id_instructivo = i.id_cab_instructivo
                INNER JOIN inst_embalaje e ON e.id = i.id_embalaje
                INNER JOIN inst_calibre c ON c.id = i.id_calibre
                INNER JOIN inst_categoria ca ON ca.id = i.id_categoria
                INNER JOIN inst_plu pl ON pl.id = i.id_plu
                INNER JOIN inst_destino d ON d.id = i.id_destino
                INNER JOIN inst_pallet p ON p.id = i.id_pallet
                INNER JOIN inst_etiqueta et ON et.id = i.id_etiqueta
                INNER JOIN inst_altura_pallet ap ON i.altura_pallet = ap.id
                INNER JOIN inst_exportadora exp ON exp.id = cab.id_exportadora
                INNER JOIN especie esp ON CAB.id_especie = esp.id_especie
                WHERE i.id_cab_instructivo = @id_instructivo AND i.version = @version
            `);

        res.json(result.recordset);
    } catch (err) {
        res.status(500).send(err.message);
    }
});


// 🟢 Iniciar servidor
const PORT = 3003;
app.listen(PORT, () => {
    console.log(`Servidor corriendo en http://192.168.19.4:${PORT}`);
});
