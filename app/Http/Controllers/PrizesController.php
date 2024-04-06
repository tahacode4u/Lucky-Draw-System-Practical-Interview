<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

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
        $prizes = Prize::all();


        if (!empty($prizes)) {
            foreach ($prizes as $k => $prize) {
                $awardValue = AwardedPrizes::where('prizes_id', $prize->id)->where('is_active', 1)->get()->value('awarded');
                $prizes[$k]['awarded'] = ($awardValue > 0) ? $awardValue:0;
                $probabilityLabels[] = $prize['title'] ."(".$prize['probability'].")";
                $probabilityData[] = $prize['probability'];
            }
        }
        $probabilityChartData = ['labels' => $probabilityLabels, 'data' => $probabilityData];
        // probability code start

        // not workable logic
            //awarded code start
            // $awardedLabels = $prizes->pluck('title');
            // $awardedData = ['12.5', '5.8', '35', '20.2', '16.5', '10'];
            // $awardedChartData = ['labels' => $awardedLabels, 'data' => $awardedData];
            //awarded code end

        return view('prizes.index', 
        [
            'prizes' => $prizes, 
            'probabilityChartData' => $probabilityChartData,
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
        // tried but not working code
        // method: random number generator in arbitrary probability distributaion fashion
        /*
        $prizes = Prize::all();
        $probability = $prizes->pluck('probability');
        $frequency = [2,13,6,17];
        $total_probability = count($probability);

        for ($i = 0; $i < $request->number_of_prizes ?? 10; $i++) {
            Prize::nextPrize($probability, $frequency, $total_probability);
        }
        */

        // for ($i = 0; $i < $request->number_of_prizes ?? 10; $i++) {
        //     Prize::nextPrize();
        // }

        $prizes = Prize::all();
        if (!empty($prizes)) {
            AwardedPrizes::query()->update(['is_active' => 0]);
            foreach ($prizes as $prize) {
                $simulation_value = $request->number_of_prizes ?? 10;
                $awarded = round(($prize->probability * $simulation_value) / 100);
                AwardedPrizes::create([
                    'prizes_id' => $prize->id, 
                    'simulation_value' => $simulation_value,
                    'awarded' => $awarded,
                    'is_active' => 1
                ]);
            }
        }

        return to_route('prizes.index');
    }

    public function reset()
    {
        AwardedPrizes::query()->update(['is_active' => 0]);
        // TODO : Write logic here
        return to_route('prizes.index');
    }
}
