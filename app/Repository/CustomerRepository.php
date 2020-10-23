<?php


namespace App\Repository;


use App\Customer;
use App\Debts;
use App\VisitReason;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use Throwable;

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

    public function all($request)
    {
        $search = $request->get('search');
        $status_id = $request->get('status_id');
        $customers = Customer::where(function ($query) use ($search) {
            return $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('last_name', 'like', '%' . $search . '%')
                ->orWhere('nickname', 'like', '%' . $search . '%')
                ->orWhere('address', 'like', '%' . $search . '%');
        })->get()
            ->map
            ->format();


        return $customers;
    }

    /**
     * @param Request $request
     * @return Customer|null
     * @throws Throwable
     */
    public function storeFromRequest(Request $request)
    {

        try {
            DB::beginTransaction();
            $customer = new Customer();
            $customer = $this->save($request, $customer);

            $this->visitRepository->setCustomer($customer);
            $this->visitRepository->setBorrowedCarboys($request->get('borrowed_carboys'));
            $this->visitRepository->setReason(VisitReason::whereReasonId(3)->first());


            $this
                ->visitRepository
                ->save($customer->latitude, $customer->longitude);

            DB::commit();
        } catch (Throwable $th) {

            DB::rollback();
            throw $th;
        }


        return $customer;
    }

    public function save(Request $request, Customer $customer, $is_edit = false)
    {
        $customer->name = $request->name;
        $customer->last_name = $request->last_name;
        $customer->nickname = $request->nickname;
        $customer->address = $request->address;
        $customer->latitude = $request->latitude;
        $customer->longitude = $request->longitude;
        if (!$is_edit) {
            $customer->last_date_visited = Carbon::now();
        }
        $customer->save();
        return $customer;
    }

    public function findById($id)
    {
        return Customer::findOrFail($id);
    }

    public function getWithSalesInfo($id)
    {

        $customer = Customer::with('lastVisits')
            ->with('carboys_movements')
            ->with('borrowed_carboys')
            ->with('lastVisits.reason')
            ->with('lastVisits.sales')
            ->with('lastVisits.sales')
            ->with('lastVisits.employee')
            ->find($id);


        $debts = Debts::with('payments')->select('debts.*', \DB::raw('convert(quantity,SIGNED) as quantity '),
            \DB::raw('(select CONVERT(ifnull(sum(quantity),0),SIGNED) from payments where debt_id = debts.id) as paid_out '))
            ->where('customer_id', $id)
            ->where('status', 'pendiente')
            ->get();

        return [
            'customer_id' => $customer->customer_id,
            'name' => $customer->name,
            'last_name' => $customer->last_name,
            'nickname' => $customer->nickname,
            'address' => $customer->address,
            'last_date_visited' => $customer->last_date_visited,
            'latitude' => $customer->latitude,
            'longitude' => $customer->longitude,
            'borrowed_carboys' => $customer->borrowed_carboys()->get('total')->sum('total'),
            'last_visits' => $customer->lastVisits,
            'carboys_movements' => $customer->carboys_movements,
            'debts' => $debts
        ];

    }


    public function generate_sales(Request $request, bool $is_new_customer = false)
    {
        $customer = null;
        if ($is_new_customer) {
            $customer = $this->storeFromRequest($request);
        } else {
            $customer = $this->findById($request->customer_id);
        }
        $es_credito = $request->get('credit') == 1;
        $this->visitRepository->setCustomer($customer);
        $this->visitRepository->setReason($es_credito ? VisitReason::find(5) : VisitReason::find(2));
        $this->visitRepository->setBorrowedCarboys($request->get('borrowed_carboys') ?? 0);
        $this->visitRepository->setReturnedCarboys($request->get('returned_carboys') ?? 0);
        $this->visitRepository->setObservations($request->get('observations'));
        $this->visitRepository->save($request->get('latitude'), $request->get('longitude'));
        $this->visitRepository->setUpcomingVisit($this->visitRepository->getVisit(), $request->get('next_visit_date'));

        if (!$es_credito) {
            return $this->visitRepository->sales($request->sales_detail);
        } else {
            return $this->visitRepository->credit($request->sales_detail);
        }


    }


}
