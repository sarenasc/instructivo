<?php
/**
 * ===========================================
 * SISTEMA DE AUTENTICACIÓN SEGURO
 * ===========================================
 * Funciones para hash y verificación de contraseñas
 * 
 * Uso:
 *   - Para nuevos usuarios: $hash = hashPassword($password)
 *   - Para login: if (verifyPassword($password, $hash)) { ... }
 */

/**
 * Generar hash seguro de contraseña
 * 
 * @param string $password Contraseña en texto plano
 * @return string Hash de la contraseña
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);
}

/**
 * Verificar contraseña contra un hash
 * 
 * @param string $password Contraseña ingresada
 * @param string $hash Hash almacenado en la BD
 * @return bool true si coincide, false si no
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Verificar si un hash necesita ser actualizado
 * (útil cuando se actualiza el algoritmo)
 * 
 * @param string $hash Hash a verificar
 * @return bool true si necesita rehash
 */
function needsRehash($hash) {
    return password_needs_rehash($hash, PASSWORD_DEFAULT, ['cost' => 12]);
}

/**
 * Generar token CSRF seguro
 * 
 * @return string Token aleatorio
 */
function generateCSRFToken() {
    return bin2hex(random_bytes(32));
}

/**
 * Verificar token CSRF
 * 
 * @param string $token Token a verificar
 * @param string $expected Token esperado
 * @return bool true si coincide
 */
function verifyCSRFToken($token, $expected) {
    return hash_equals($expected, $token);
}
