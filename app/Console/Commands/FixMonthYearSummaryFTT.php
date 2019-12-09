<?php

namespace App\Console\Commands;

use App\Models\Summary;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixMonthYearSummaryFTT extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:summary_ftt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        Summary::where('status',1)->whereNotNull('ngay_thanh_toan')->where('type','!=',1)->update([
            'month'=>DB::raw('month(ngay_thanh_toan)'),
            'year'=>DB::raw('year(ngay_thanh_toan)')
        ]);
    }
}
