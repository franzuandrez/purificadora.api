<?php


namespace App\Repository;


use App\Customer;
use App\Employee;
use App\VisitReason;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use phpDocumentor\Reflection\Types\Boolean;

class CustomerRepository
{


    /**
     * @var VisitRepository
     */
    private $visitRepository;

    public function __construct(VisitRepository $visitRepository)
    {
        $this->visitRepository = $visitRepository;
    }

    public function all()
    {
        $customers = Customer::get()->map->format();

        return $customers;
    }

    /**
     * @param Request $request
     * @return Customer|null
     */
    public function storeFromRequest(Request $request)
    {

        try {
            DB::beginTransaction();
            $customer = new Customer();
            $customer = $this->save($request, $customer);


            $this->visitRepository->setCustomer($customer);
            $this->visitRepository->setReason(VisitReason::first());


            $this
                ->visitRepository
                ->save($customer->latitude, $customer->longitude);

            DB::commit();
        } catch (\Exception $ex) {

            DB::rollback();
            $customer = null;
        }


        return $customer;
    }

    private function save(Request $request, Customer $customer)
    {
        $customer->name = $request->name;
        $customer->last_name = $request->last_name;
        $customer->nickname = $request->nickname;
        $customer->address = $request->address;
        $customer->latitude = $request->latitude;
        $customer->longitude = $request->longitude;
        $customer->last_date_visited = Carbon::now();
        $customer->save();
        return $customer;
    }

    public function findById($id)
    {
        return Customer::findOrFail($id);
    }


    public function generate_sales(Request $request, bool $is_new_customer = false)
    {
        $customer = null;
        if ($is_new_customer) {
            $customer = $this->storeFromRequest($request);
        } else {
            $customer = $this->findById($request->customer_id);
        }

        $this->visitRepository->setCustomer($customer);
        $this->visitRepository->setReason(VisitReason::find(2));
        $this->visitRepository->save($request->latitude, $request->longitude);

        return $this->visitRepository->sales($request->sales_detail);


    }

}
