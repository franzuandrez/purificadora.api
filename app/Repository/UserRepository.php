<?php


namespace App\Repository;

use Illuminate\Http\Request;
use App\User;

class UserRepository
{

    private $employeeRepository;

    public function __construct(EmployeeRepository $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }

    public function all()
    {
        return User::get();
    }


    public function storeFromRequest(Request $request)
    {

        $user = new User();
        $user = $this->save($user, $request->all());
        $this->employeeRepository->storeFromRequest($request, $user);

        return $user;

    }

    public function updateFromRequest(Request $request, $id)
    {
        $user = $this->findById($id);
        $this->employeeRepository->updateFromRequest($request, $id);
        return $user->refresh();
    }

    /**
     * @param $id
     * @return User|null
     */
    public function findById($id)
    {
        return $user = User::find($id);
    }



    private function save(User $user, array $attributes)
    {
        $user->username = $attributes['username'];
        $user->password = bcrypt($attributes['password']);
        $user->save();

        return $user;
    }


}
