import os
import re

BASE_DIR = r"C:\xampp\htdocs\instructivo\app"

# Mapeo de archivos HTML a PHP con sus titulos y scripts
ARCHIVOS = [
    # Configuracion
    ("Configuracion/embalaje.html", "Configuracion/embalaje.php", "Gestión de Embalajes", "../assets/js/embalaje.js"),
    ("Configuracion/etiqueta.html", "Configuracion/etiqueta.php", "Gestión de Etiquetas", "../assets/js/etiqueta.js"),
    ("Configuracion/pallet.html", "Configuracion/pallet.php", "Gestión de Pallets", "../assets/js/pallet.js"),
    ("Configuracion/plu.html", "Configuracion/plu.php", "Gestión de PLUs", "../assets/js/plu.js"),
    ("Configuracion/exportadora.html", "Configuracion/exportadora.php", "Gestión de Exportadoras", "../assets/js/exportadora.js"),
    ("Configuracion/destino.html", "Configuracion/destino.php", "Gestión de Destinos", "../assets/js/destino.js"),
    ("Configuracion/inst_altura_pallet.html", "Configuracion/inst_altura_pallet.php", "Configuración Altura Pallet", "../assets/js/inst_altura_pallet.js"),
    ("Configuracion/edicion_config.html", "Configuracion/edicion_config.php", "Edición de Configuración", "../assets/js/edicion_config.js"),
    
    # Procesos
    ("Procesos/Pedidos.html", "Procesos/Pedidos.php", "Agregar Pedidos", "../assets/js/Pedidos.js"),
    ("Procesos/exportar_instructivo.html", "Procesos/exportar_instructivo.php", "Exportar Instructivo", "../assets/js/exportar_instructivo.js"),
    ("Procesos/copiar_instructivo.html", "Procesos/copiar_instructivo.php", "Copiar Instructivo", "../assets/js/copiar_instructivo.js"),
    ("Procesos/mostrar_instructivo.html", "Procesos/mostrar_instructivo.php", "Mostrar Instructivo", "../assets/js/mostrar_instructivo.js"),
]

def convertir_html_a_php(html_path, php_path, titulo, script):
    """Convierte un archivo HTML a PHP con componentes compartidos"""
    
    full_html = os.path.join(BASE_DIR, html_path)
    full_php = os.path.join(BASE_DIR, php_path)
    
    if not os.path.exists(full_html):
        print(f"  [NO EXISTE] {html_path}")
        return False
    
    # Leer el archivo HTML
    with open(full_html, 'r', encoding='utf-8') as f:
        contenido = f.read()
    
    # Extraer el contenido principal (entre el nav y el cierre del body)
    # Buscar el div.container o div.container-fluid principal
    match = re.search(r'<nav[^>]*>.*?</nav>(.*?)<script[^>]*>.*?</script>\s*</body>', contenido, re.DOTALL)
    
    if match:
        contenido_principal = match.group(1).strip()
        
        # Crear el archivo PHP
        php_content = f"""<?php
$titulo_pagina = '{titulo}';
require_once __DIR__ . '/../includes/header.php';
?>

{contenido_principal}

<?php
$scripts_extra = '<script src="{script}"></script>';
require_once __DIR__ . '/../includes/footer.php';
?>
"""
        
        # Escribir el archivo PHP
        with open(full_php, 'w', encoding='utf-8') as f:
            f.write(php_content)
        
        print(f"  [OK] {html_path} -> {php_path}")
        return True
    else:
        print(f"  [ERROR] No se pudo extraer contenido de {html_path}")
        return False

def main():
    print("=" * 60)
    print("  CONVERTIDOR HTML A PHP - MENU COMPARTIDO")
    print("=" * 60)
    print()
    
    exitosos = 0
    fallidos = 0
    
    for html, php, titulo, script in ARCHIVOS:
        print(f"Convirtiendo: {html}")
        if convertir_html_a_php(html, php, titulo, script):
            exitosos += 1
        else:
            fallidos += 1
    
    print()
    print("=" * 60)
    print(f"  COMPLETADO: {exitosos} exitosos, {fallidos} fallidos")
    print("=" * 60)
    print()
    input("Presiona Enter para salir...")

if __name__ == "__main__":
    main()
