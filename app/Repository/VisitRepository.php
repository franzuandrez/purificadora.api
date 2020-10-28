<?php


namespace App\Repository;


use App\CarboyMovement;
use App\Customer;
use App\Debts;
use App\Employee;
use App\Inventory;
use App\Payment;
use App\Sales;
use App\SalesDetail;
use App\UpcomingVisit;
use App\User;
use App\Visit;
use App\VisitReason;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class VisitRepository
{


    private $observations = null;

    /**
     * @return null
     */
    public function getObservations()
    {
        return $this->observations;
    }

    /**
     * @param null $observations
     */
    public function setObservations($observations): void
    {
        $this->observations = $observations;
    }

    private $borrowed_carboys = 0;

    /**
     * @return int
     */
    public function getBorrowedCarboys(): int
    {
        return $this->borrowed_carboys;
    }

    /**
     * @param int $borrowed_carboys
     */
    public function setBorrowedCarboys(int $borrowed_carboys = 0): void
    {
        $this->borrowed_carboys = $borrowed_carboys;
    }

    /**
     * @return int
     */
    public function getReturnedCarboys(): int
    {
        return $this->returned_carboys;
    }

    /**
     * @param int $returned_carboys
     */
    public function setReturnedCarboys(int $returned_carboys = 0): void
    {
        $this->returned_carboys = $returned_carboys;
    }

    private $returned_carboys = 0;
    /**
     * @var SalesRepository
     */
    private $salesRepository;
    /**
     * @var Customer|null
     */
    private $customer = null;
    /**
     * @var Employee|null
     */
    private $employee = null;
    /**
     * @var VisitReason|null
     */
    private $reason = null;

    /**
     * @var Visit|null
     */
    private $visit = null;
    /**
     * @var ReasonRepository
     */
    private $reasonRepository = null;

    public function __construct(SalesRepository $salesRepository, ReasonRepository $reasonRepository)
    {

        $this->salesRepository = $salesRepository;
        $this->reasonRepository = $reasonRepository;

    }


    /**
     * @return Visit|null
     */
    public function getVisit(): ?Visit
    {
        return $this->visit;
    }

    /**
     * @param Visit|null $visit
     */
    public function setVisit(?Visit $visit): void
    {
        $this->visit = $visit;
    }

    /**
     * @return Customer|null
     */
    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    /**
     * @param Customer|null $customer
     */
    public function setCustomer(?Customer $customer): void
    {
        $this->customer = $customer;
    }

    /**
     * @return Employee|null
     */
    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    /**
     * @param Employee|null $employee
     */
    public function setEmployee(?Employee $employee): void
    {
        $this->employee = $employee;
    }

    /**
     * @return VisitReason|null
     */
    public function getReason(): ?VisitReason
    {
        return $this->reason;
    }

    /**
     * @param VisitReason|null $reason
     */
    public function setReason(?VisitReason $reason): void
    {
        $this->reason = $reason;
    }


    public function all($request)
    {

        $search = $request->get('search');
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $employee_id = $request->get('employee_id');
        $reason_id = $request->get('reason_id');

        $visits = Visit::with('customer')
            ->with('employee')
            ->with('reason')
            ->select('visit.*')
            ->join('customer', 'customer.customer_id', '=', 'visit.customer_id')
            ->join('employee', 'employee.employee_id', '=', 'visit.employee_id')
            ->join('visit_reason', 'visit_reason.reason_id', '=', 'visit.reason_id')
            ->where(function ($query) use ($search) {
                return $query->orWhere('customer.name', 'LIKE', '%' . $search . '%')
                    ->orWhere('customer.nickname', 'LIKE', '%' . $search . '%')
                    ->orWhere('employee.name', 'LIKE', '%' . $search . '%')
                    ->orWhere('employee.last_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('visit_reason.description', 'LIKE', '%' . $search . '%');
            })
            ->orderBy('visited_date', 'desc');
        if ($reason_id != "") {
            $visits = $visits->where('visit.reason_id', $reason_id);
        }
        if ($employee_id != "") {
            $visits = $visits->where('visit.employee_id', $employee_id);
        }

        $visits = $visits->paginate(20);

        return ($visits);

    }


    public function findById($id)
    {


        return Visit::whereVisitId($id)
            ->with('customer')
            ->with('employee')
            ->with('reason')
            ->with('sales')
            ->with('carboys_movements')
            ->without('carboys_movements.visit')
            ->first();
    }

    public function save(float $lat, float $lon, $next_visit_date = null)
    {


        $visit = new Visit();
        $visit->employee_id = Auth::user()->employee->employee_id;
        $visit->customer_id = $this->getCustomer()->customer_id;
        $visit->reason_id = $this->getReason()->reason_id;
        $visit->latitude = $this->getCustomer()->latitude;
        $visit->longitude = $this->getCustomer()->longitude;
        $visit->visited_date = Carbon::now();
        $visit->save();

        $customer = $this->getCustomer();
        $customer->last_date_visited = Carbon::now();
        $customer->save();

        $this->visit = $visit;
        $this->setCarboyMovement($this->getBorrowedCarboys(), $this->getObservations());
        $this->setCarboyMovement($this->getReturnedCarboys(), $this->getObservations(), 'R');
        $this->setUpcomingVisit($visit, $next_visit_date);

        return $visit;

    }

    public function visit_by_reason($request)
    {
        $lat = $request->get('latitude');
        $lon = $request->get('longitude');
        $reason_id = $request->get('reason_id');
        $customer_id = $request->get('customer_id');
        $this->setBorrowedCarboys($request->get('borrowed_carboys') ?? 0);
        $this->setReturnedCarboys($request->get('returned_carboys') ?? 0);
        $this->setObservations($request->get('observations'));
        $reason = $this->reasonRepository->findById($reason_id);
        $this->setCustomer(Customer::find($customer_id));
        $this->setReason($reason);
        return $this->save($lat, $lon, $request->get('next_visit_date'));


    }


    /**
     * @param array $detail
     * @return Sales
     */
    public
    function sales(array $detail)
    {

        $sales_detail = collect($detail)->map(function ($item) {
            return new SalesDetail([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'unit_price_discount' => $item['unit_price_discount'],
                'subtotal' => $item['subtotal'],
                'total' => $item['total'],
            ]);
        });

        $sales = $this->salesRepository->generate($this->visit, $sales_detail);

        return $sales;
    }

    public function credit(array $detail)
    {

        collect($detail)->each(function ($item) {
            $det = new Debts([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total' => $item['total'],
                'visit_id' => $this->getVisit()->visit_id,
                'customer_id' => $this->getCustomer()->customer_id,
                'status' => 'pendiente',
            ]);
            $det->save();
        });
        return $detail;

    }


    public function pay($request)
    {

        $debts = collect($request->debts);
        $lat = $request->get('latitude');
        $lon = $request->get('longitude');
        $this->setBorrowedCarboys($request->get('borrowed_carboys') ?? 0);
        $this->setReturnedCarboys($request->get('returned_carboys') ?? 0);
        $this->setObservations($request->get('observations'));
        $this->setCustomer(Customer::find($request->get('customer_id')));
        $this->setReason(VisitReason::find(6));
        $this->save($lat, $lon, $request->get('next_visit_date'));
        $debts->each(function ($item) {
            $debt = Debts::with('payments')->findOrFail($item['debt_id']);
            if ($debt != null) {
                ($quantity = intval($debt->payments->sum('quantity')));
                if ($debt->quantity >= $quantity + $item['quantity']) {
                    $payment = new Payment();
                    $payment->debt_id = $item['debt_id'];
                    $payment->quantity = $item['quantity'];
                    $payment->total = $item['total'];
                    $payment->employee_id = Auth::user()->employee->employee_id;
                    $payment->customer_id = $debt->customer_id;
                    $payment->visit_id = $this->getVisit()->visit_id;
                    $payment->product_id = $debt->product_id;
                    $payment->price = $debt->price;
                    $payment->save();
                }
                if (intval($debt->quantity) == ($quantity + $item['quantity'])) {
                    $debt->payment_date = Carbon::now();
                    $debt->status = 'pagado';
                    $debt->employee_collector_id = Auth::user()->employee->employee_id;
                    $debt->save();
                }
            }

        });
        return $this->getVisit();

    }


    public function setCarboyMovement($quantity = 0, $observations = null, $type = 'B')
    {
        if ($quantity > 0) {
            $carboy_movement = new CarboyMovement();
            $carboy_movement->quantity = $quantity;
            $carboy_movement->observations = $observations;
            $carboy_movement->type = $type;
            $carboy_movement->visit_id = $this->getVisit()->visit_id;
            $carboy_movement->customer_id = $this->getVisit()->customer_id;
            $carboy_movement->save();

            $inventory = new Inventory();
            $inventory->quantity = $quantity;
            $inventory->type = $type;
            $inventory->done_by = Auth::id();
            $inventory->movement_date = Carbon::now();
            $inventory->product_id = 1;
            $inventory->document_id = $carboy_movement->visit_id;
            $inventory->document_type = 'V';
            $inventory->save();
        }
    }

    public function setUpcomingVisit(Visit $visit, $next_visit_date)
    {

        if ($next_visit_date) {
            $upcoming_visit = new UpcomingVisit();
            $upcoming_visit->customer_id = $visit->customer_id;
            $upcoming_visit->next_visit_date = $next_visit_date;
            $upcoming_visit->employee_id = Auth::id();
            $upcoming_visit->save();
        }

    }


}
