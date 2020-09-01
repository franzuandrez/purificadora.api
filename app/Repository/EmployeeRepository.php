<?php


namespace App\Repository;

use App\Employee;
use App\User;
use Illuminate\Http\Request;

class EmployeeRepository
{


    public function all()
    {

    }

    public function storeFromRequest(Request $request, User $user)
    {

        $employee = new Employee();
        $employee->user_id = $user->id;
        $employee = $this->save($employee, $request->all());

        return $employee;

    }

    public function updateFromRequest(Request $request, $id)
    {

        $employee = $this->findById($id);
        $employee = $this->save($employee, $request->all());

        return $employee;
    }

    public function findById($id)
    {

        return Employee::findOrFail($id);
    }

    private function save(Employee $employee, array $attributes)
    {

        $employee->name = $attributes['name'];
        $employee->last_name = $attributes['last_name'];
        $employee->status = 1;
        $employee->save();

        return $employee;
    }

}
