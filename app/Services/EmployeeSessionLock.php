<?php

namespace App\Services;

class EmployeeSessionLock extends AdminSessionLock
{
    protected const SLOT = 2;
    protected const SESSION_KEY = 'employee_access_token';

    public const MESSAGE = 'El sistema ya está siendo utilizado por un empleado. Intenta nuevamente cuando cierre su sesión.';
}
