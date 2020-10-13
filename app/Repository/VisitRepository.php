<?php


namespace App\Repository;


use App\CarboyMovement;
use App\Customer;
use App\Employee;
use App\Sales;
use App\SalesDetail;
use App\Visit;
use App\VisitReason;
use Carbon\Carbon;
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
    public function setBorrowedCarboys(int $borrowed_carboys): void
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
    public function setReturnedCarboys(int $returned_carboys): void
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


    public function all()
    {
        return (Visit::with('customer')
            ->with('employee')
            ->with('reason')
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

        $this->setCarboyMovement($this->getBorrowedCarboys(), $this->getObservations());
        $this->setCarboyMovement($this->getReturnedCarboys(), $this->getObservations());

        $this->visit = $visit;

        return $visit;

    }

    public function visit_by_reason($request)
    {
        $lat = $request->get('latitude');
        $lon = $request->get('longitude');
        $reason_id = $request->get('reason_id');
        $customer_id = $request->get('customer_id');
        $this->setBorrowedCarboys($request->get('borrowed_carboys'));
        $this->setReturnedCarboys($request->get('returned_carboys'));
        $this->setObservations($request->get('observations'));
        $reason = $this->reasonRepository->findById($reason_id);
        $this->setCustomer(Customer::find($customer_id));
        $this->setReason($reason);
        $this->save($lat, $lon);


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
            $carboy_movement->visit_id = $this->getVisit();
            $carboy_movement->save();
        }
    }


}
