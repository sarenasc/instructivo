<?php
/**
 * ===========================================
 * VALIDADOR REUTILIZABLE
 * ===========================================
 * Clase para validación de formularios
 */

class Validator {
    
    /**
     * Validar campo requerido
     * @param mixed $field Valor del campo
     * @param string $fieldName Nombre del campo para el mensaje
     * @return string|null Mensaje de error o null si está válido
     */
    public static function required($field, $fieldName) {
        if (empty($field) && $field !== '0') {
            return "El campo $fieldName es obligatorio";
        }
        return null;
    }
    
    /**
     * Validar email
     * @param string $email Email a validar
     * @return string|null Mensaje de error o null si está válido
     */
    public static function email($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "El email no es válido";
        }
        return null;
    }
    
    /**
     * Validar número entero
     * @param mixed $number Número a validar
     * @param string $fieldName Nombre del campo para el mensaje
     * @return string|null Mensaje de error o null si está válido
     */
    public static function integer($number, $fieldName) {
        if (!filter_var($number, FILTER_VALIDATE_INT)) {
            return "El campo $fieldName debe ser un número entero";
        }
        return null;
    }
    
    /**
     * Validar número decimal
     * @param mixed $number Número a validar
     * @param string $fieldName Nombre del campo para el mensaje
     * @return string|null Mensaje de error o null si está válido
     */
    public static function numeric($number, $fieldName) {
        if (!is_numeric($number)) {
            return "El campo $fieldName debe ser un número";
        }
        return null;
    }
    
    /**
     * Validar longitud mínima
     * @param string $field Valor del campo
     * @param int $min Longitud mínima
     * @param string $fieldName Nombre del campo para el mensaje
     * @return string|null Mensaje de error o null si está válido
     */
    public static function minLength($field, $min, $fieldName) {
        if (strlen($field) < $min) {
            return "El campo $fieldName debe tener al menos $min caracteres";
        }
        return null;
    }
    
    /**
     * Validar longitud máxima
     * @param string $field Valor del campo
     * @param int $max Longitud máxima
     * @param string $fieldName Nombre del campo para el mensaje
     * @return string|null Mensaje de error o null si está válido
     */
    public static function maxLength($field, $max, $fieldName) {
        if (strlen($field) > $max) {
            return "El campo $fieldName no puede exceder $max caracteres";
        }
        return null;
    }
    
    /**
     * Validar rango numérico
     * @param mixed $number Número a validar
     * @param int $min Valor mínimo
     * @param int $max Valor máximo
     * @param string $fieldName Nombre del campo para el mensaje
     * @return string|null Mensaje de error o null si está válido
     */
    public static function range($number, $min, $max, $fieldName) {
        if (!is_numeric($number) || $number < $min || $number > $max) {
            return "El campo $fieldName debe estar entre $min y $max";
        }
        return null;
    }
    
    /**
     * Validar que dos campos sean iguales (útil para contraseñas)
     * @param mixed $field1 Primer campo
     * @param mixed $field2 Segundo campo
     * @param string $fieldName1 Nombre del primer campo
     * @param string $fieldName2 Nombre del segundo campo
     * @return string|null Mensaje de error o null si está válido
     */
    public static function matches($field1, $field2, $fieldName1, $fieldName2) {
        if ($field1 !== $field2) {
            return "Los campos $fieldName1 y $fieldName2 no coinciden";
        }
        return null;
    }
    
    /**
     * Validar formato de fecha (YYYY-MM-DD)
     * @param string $date Fecha a validar
     * @return string|null Mensaje de error o null si está válida
     */
    public static function date($date) {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        if (!$d || $d->format('Y-m-d') !== $date) {
            return "El formato de fecha debe ser YYYY-MM-DD";
        }
        return null;
    }
    
    /**
     * Validar que el valor sea único en la base de datos
     * @param resource $conn Conexión a BD
     * @param string $table Nombre de la tabla
     * @param string $field Nombre del campo
     * @param mixed $value Valor a verificar
     * @param int|null $excludeId ID a excluir (para modificaciones)
     * @return string|null Mensaje de error o null si está válido
     */
    public static function unique($conn, $table, $field, $value, $excludeId = null) {
        $value_safe = str_replace("'", "''", $value);
        $sql = "SELECT COUNT(*) as count FROM $table WHERE $field = '$value_safe'";
        
        if ($excludeId !== null) {
            $sql .= " AND id != $excludeId";
        }
        
        $stmt = sqlsrv_query($conn, $sql);
        if (!$stmt) {
            return "Error verificando unicidad";
        }
        
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        sqlsrv_free_stmt($stmt);
        
        if ($row['count'] > 0) {
            return "Ya existe un registro con ese $field";
        }
        
        return null;
    }
    
    /**
     * Validar múltiples reglas a la vez
     * @param array $rules Reglas de validación
     * @return array Lista de errores
     */
    public static function validate($rules) {
        $errors = [];
        
        foreach ($rules as $rule) {
            $error = call_user_func_array($rule['method'], array_slice($rule, 1));
            if ($error !== null) {
                $errors[] = $error;
            }
        }
        
        return $errors;
    }
    
    /**
     * Retornar errores como string HTML
     * @param array $errors Lista de errores
     * @return string HTML con errores
     */
    public static function errorsToHtml($errors) {
        if (empty($errors)) {
            return '';
        }
        
        $html = '<div class="alert alert-danger">';
        foreach ($errors as $error) {
            $html .= "<div>❌ $error</div>";
        }
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Retornar errores como JSON
     * @param array $errors Lista de errores
     * @return string JSON con errores
     */
    public static function errorsToJson($errors) {
        return json_encode([
            'success' => false,
            'errors' => $errors
        ]);
    }
}
