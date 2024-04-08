<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use DB;
use App\Models\Prize;
use App\Models\AwardedPrizes;
use App\Rules\ValidateProbability;
use App\Http\Requests\PrizeRequest;
use Illuminate\Http\Request;



class PrizesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $probabilityLabels = $probabilityData = $awardedLabels = $awardedData = [];
        $probabilityChartData = ['labels' => $probabilityLabels, 'data' => $probabilityData];
        $awardedChartData = ['labels' => $awardedLabels, 'data' => $awardedData];

        // probability code start
        $prizes = Prize::withCount('getAwarded')->get();

        if (!empty($prizes)) {
            foreach ($prizes as $k => $prize) {
                $probabilityLabels[] = $prize['title'] ." (".$prize['probability']."%)";
                $probabilityData[] = $prize['probability'];
            }
        }
        $probabilityChartData = ['labels' => $probabilityLabels, 'data' => $probabilityData];
        // probability code start

        //awarded code start
        $awardedPrizes = AwardedPrizes::join('prizes', 'prizes.id', '=' ,'awarded_prizes.prizes_id')
        ->selectRaw('prizes.title, awarded_prizes.prizes_id, awarded_prizes.simulation_value, count(awarded_prizes.prizes_id) as awarded_count')
        ->groupBy('awarded_prizes.prizes_id', 'awarded_prizes.simulation_value', 'prizes.title')
        ->get();

        if (!empty($awardedPrizes)) {
            foreach ($awardedPrizes as $awardPrize) {
                $percentage = ($awardPrize->awarded_count * 100) / $awardPrize->simulation_value;
                $awardedLabels[] = $awardPrize->title . " (" . number_format($percentage, 2) . "%)";
                $awardedData[] = $percentage;
            }
        }

        $awardedChartData = ['labels' => $awardedLabels, 'data' => $awardedData];
        //awarded code end

        return view('prizes.index', 
        [
            'prizes' => $prizes, 
            'probabilityChartData' => $probabilityChartData,
            'awardedChartData' => $awardedChartData,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('prizes.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  PrizeRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PrizeRequest $request)
    {

        $request->validate([
            'probability' => [new ValidateProbability],
        ]);

        $prize = new Prize;
        $prize->title = $request->input('title');
        $prize->probability = floatval($request->input('probability'));
        $prize->save();

        return to_route('prizes.index');
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $prize = Prize::findOrFail($id);
        return view('prizes.edit', ['prize' => $prize]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  PrizeRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(PrizeRequest $request, $id)
    {
        $request->validate([
            'probability' => [new ValidateProbability($id)],
        ]);

        $prize = Prize::findOrFail($id);
        $prize->title = $request->input('title');
        $prize->probability = floatval($request->input('probability'));
        $prize->save();

        return to_route('prizes.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $prize = Prize::findOrFail($id);
        $prize->delete();

        return to_route('prizes.index');
    }


    public function simulate(Request $request)
    {
        AwardedPrizes::query()->delete();
        $total_prizes = $request->number_of_prizes ?? 10;
        $prizes = Prize::all();
        $probability = $prizes->pluck('id')->toArray();
        for ($i = 0; $i < $total_prizes; $i++) {
            Prize::nextPrize($probability, $total_prizes);
        }

        return to_route('prizes.index');
    }

    public function reset()
    {
        AwardedPrizes::query()->delete();
        return to_route('prizes.index');
    }
}
