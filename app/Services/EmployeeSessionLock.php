<?php

namespace App\Services;

class EmployeeSessionLock extends AdminSessionLock
{
    protected const SLOT = 2;
    protected const SESSION_KEY = 'employee_access_token';

    public const MESSAGE = 'Hay una sesión de empleado reservada recientemente. Cierra la sesión anterior o espera 1 minuto sin actividad e intenta de nuevo.';
}
