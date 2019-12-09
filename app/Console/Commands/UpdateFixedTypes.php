<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Repositories\Contracts\TypeRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class UpdateFixedTypes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:update-fixed-types';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';


    /*
    * Type repository
    */
    protected $typeRepository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(TypeRepositoryInterface $typeRepository)
    {
        parent::__construct();
        $this->typeRepository = $typeRepository;
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {  
        // Load all fixed type
        $fixedType = config('type.first_types');
        $invalidTypes= [];
        foreach($fixedType as $type) {
            // Kiểm tra xem id cố định có lớn hơn 1000 không
            if($type['id'] <= 1000) {
                $this->typeRepository->saveData([$type]);
                continue;
            }

            $invalidTypes[] = $type;
        }

    }



}
