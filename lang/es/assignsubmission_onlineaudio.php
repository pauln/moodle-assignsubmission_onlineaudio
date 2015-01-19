<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see http://www.gnu.org/licenses/.

/**
 * Strings for component 'assignsubmission_onlineaudio', language 'es'
 *
 * @package assignsubmission_onlineaudio
 * @copyright 2012 Paul Nicholls
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$string['pluginname'] = 'Grabación de audio online';
$string['recording'] = 'Grabación de audio online';

$string['enabled'] = 'Grabación de audio online';
$string['enabled_help'] = 'Si está activo, los alumnos podrán grabar audio como tarea.';

$string['configmaxbytes'] = 'Tamaño máximo del archivo';
$string['maxbytes'] = 'Tamaño máximo del archivo';

$string['maxfilessubmission'] = 'Número máximo de grabaciones';
$string['maxfilessubmission_help'] = 'Si la grabación de audio está habilitada, cada estudiante podrá subir el número de grabaciones permitidas.';

$string['defaultname'] = 'Nombre del archivo por defecto';
$string['defaultname_help'] = 'Está opción puede ser usada para pre-completar el nombre del archivo basado en un patrón. El nombre predefinido puede ser forzado configurando: "Permitir a los alumnos cambiar el nombre del archivo" a "No".';
$string['nodefaultname'] = 'Ninguno (en blanco)';
$string['defaultname1'] = 'username_assignment_course_date';
$string['defaultname2'] = 'fullname_assignment_course_date';

$string['allownameoverride'] = 'Permitir a los alumnos cambiar el nombre del archivo';
$string['allownameoverride_help'] = 'Si está habilitado los estudiantes podrán sobreescribir el nombre del archivo por defecto y elegir uno. Está opción no tiene efecto si el patrón del nombre por defecto está en ninguno (blanco) ya que hay que especificar un nombre';

$string['countfiles'] = '{$a} Archivos';
$string['nosuchfile'] = 'No se encontro ese archivo.';
$string['confirmdeletefile'] = '¿Está seguro que quiere borrar el archivo {$a}?';
$string['upload'] = 'Subir';
$string['uploaderror'] = 'Error subiendo grabación.';
$string['maxfilesreached'] = 'Ya ha completado el núermo de grabaciones máximas que tiene permitidas para esta tarea.';