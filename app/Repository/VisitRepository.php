<?php


namespace App\Repository;


use App\CarboyMovement;
use App\Customer;
use App\Employee;
use App\Inventory;
use App\Sales;
use App\SalesDetail;
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

        return (Visit::with('customer')
            ->with('employee')
            ->with('reason')
            ->where(function ($query) use ($search) {
                return $query->orwhereHas('employee', function (Builder $q) use ($search) {
                    $q->orwhere('name', 'like', '%' . $search . '%');
                })->orwhereHas('reason', function (Builder $q) use ($search) {
                    $q->orwhere('description', 'like', '%' . $search . '%');
                })->orwhereHas('customer', function (Builder $q) use ($search) {
                    $q->orwhere('name', 'like', '%' . $search . '%')
                        ->orwhere('last_name', 'like', '%' . $search . '%')
                        ->orwhere('nickname', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('visited_date', 'desc')
            ->paginate(15));
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

    public function save(float $lat, float $lon)
    {


        $visit = new Visit();
        $visit->employee_id = Auth::user()->employee->employee_id;
        $visit->customer_id = $this->getCustomer()->customer_id;
        $visit->reason_id = $this->getReason()->reason_id;
        $visit->latitude = $lat;
        $visit->longitude = $lon;
        $visit->visited_date = Carbon::now();
        $visit->save();

        $this->visit = $visit;
        $this->setCarboyMovement($this->getBorrowedCarboys(), $this->getObservations());
        $this->setCarboyMovement($this->getReturnedCarboys(), $this->getObservations(), 'R');


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
        return $this->save($lat, $lon);


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
            $inventory->document_id =   $carboy_movement->visit_id ;
            $inventory->document_type = 'V';
            $inventory->save();
        }
    }


}
