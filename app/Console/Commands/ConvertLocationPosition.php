<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Entity\LocationPoint;
use Carbon\Carbon;

class ConvertLocationPosition extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'convert:location';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert Location\'s position to points array';

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
        $colorPalette = [
            "#000","#444","#666","#999","#ccc","#eee","#f3f3f3","#fff",
            "#f00","#f90","#ff0","#0f0","#0ff","#00f","#90f","#f0f",
            "#f4cccc","#fce5cd","#fff2cc","#d9ead3","#d0e0e3","#cfe2f3","#d9d2e9","#ead1dc",
            "#ea9999","#f9cb9c","#ffe599","#b6d7a8","#a2c4c9","#9fc5e8","#b4a7d6","#d5a6bd",
            "#e06666","#f6b26b","#ffd966","#93c47d","#76a5af","#6fa8dc","#8e7cc3","#c27ba0",
            "#c00","#e69138","#f1c232","#6aa84f","#45818e","#3d85c6","#674ea7","#a64d79",
            "#900","#b45f06","#bf9000","#38761d","#134f5c","#0b5394","#351c75","#741b47",
            "#600","#783f04","#7f6000","#274e13","#0c343d","#073763","#20124d","#4c1130"
        ];

        $color_count = count($colorPalette);

        foreach (LocationPoint::all() as $index => $location) {
            
            if (!$location->points || $location->color == $colorPalette[0] || $location->updated_at > Carbon::create(2018, 12, 17, 10, 58, 0, 'Asia/Kuala_Lumpur')) {
                $pos_x = $location->position_x < 0 ? 0 : $location->position_x;
                $pos_y = $location->position_y < 0 ? 0 : $location->position_y;
                $points = [ $pos_x, $pos_y, $pos_x + 100, $pos_y, $pos_x + 100, $pos_y + 100, $pos_x, $pos_y + 100 ];
                
                $location->points = implode(',', $points);
                $location->color = $colorPalette[$index % $color_count];
                $location->save();
            }
        }
    }
}   
