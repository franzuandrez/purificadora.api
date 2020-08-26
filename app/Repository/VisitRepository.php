<?php


namespace App\Repository;


use App\Customer;
use App\Employee;
use App\Visit;
use App\VisitReason;
use Carbon\Carbon;

class VisitRepository
{

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


    public function save($lat, $lon)
    {

        $visit = new Visit();
        $visit->employee_id = $this->getEmployee()->employee_id;
        $visit->customer_id = $this->getCustomer()->customer_id;
        $visit->reason_id = $this->getReason()->reason_id;
        $visit->latitude = $lat;
        $visit->longitude = $lon;
        $visit->visited_date = Carbon::now();
        $visit->save();

        return $visit;

    }


}
