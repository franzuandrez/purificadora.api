<?php


namespace App\Repository;


use App\VisitReason;
use Illuminate\Http\Request;

class ReasonRepository
{

    public function all()
    {

        return VisitReason::where('mobile_show', 1)->get();

    }

    public function storeFromRequest(Request $request)
    {
        $visitReason = new VisitReason();
        $visitReason = $this->save($visitReason, $request->all());

        return $visitReason;
    }

    public function updateFromRequest(Request $request, $id)
    {
        $visitReason = $this->findById($id);
        $visitReason = $this->save($visitReason, $request->all());

        return $visitReason;
    }

    public function findById($id)
    {
        return VisitReason::findOrFail($id);
    }

    private function save(VisitReason $visitReason, array $attributes)
    {

        $visitReason->description = $attributes['description'];
        $visitReason->save();

        return $visitReason;
    }

}
