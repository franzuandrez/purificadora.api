<?php


namespace App\Repository;


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

    public function __construct(SalesRepository $salesRepository)
    {

        $this->salesRepository = $salesRepository;

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
        return(Visit::with('customer')
            ->with('employee')
            ->with('reason')
            ->orderBy('visited_date','desc')
            ->paginate(15));
    }

    public function save($lat, $lon)
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
        return $visit;

    }


    /**
     * @param array $detail
     * @return Sales
     */
    public function sales(array $detail)
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


}
