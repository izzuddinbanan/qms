<?php

use App\Entity\Status;
use Illuminate\Database\Seeder;

class StatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        return collect([
            [
                'internal'           => 'Lodged',
                'external'           => 'Lodged',
                'internal_color'     => '#2952D6',
                'external_color'     => '#2952D6',
            ],
            [
                'internal'           => 'New',
                'external'           => 'Lodged',
                'internal_color'     => '#F99100',
                'external_color'     => '#2952D6',

            ],
            [
                'internal'           => 'Pending Start',
                'external'           => 'Lodged',
                'internal_color'     => '#BD5607',
                'external_color'     => '#2952D6',

            ],
            [
                'internal'           => 'VOID',
                'external'           => 'Rejected',
                'internal_color'     => '#EFC089',
                'external_color'     => '#FF3939',
            ],
            [
                'internal'           => 'W.I.P',
                'external'           => 'W.I.P',
                'internal_color'     => '#EFD000',
                'external_color'     => '#EFD000',

            ],
            [
                'internal'           => 'Not Me',
                'external'           => 'W.I.P',
                'internal_color'     => '#1C9DFF',
                'external_color'     => '#EFD000',
            ],
            [
                'internal'           => 'Reassign',
                'external'           => 'W.I.P',
                'internal_color'     => '#7F7F7F',
                'external_color'     => '#EFD000',

            ],
            [
                'internal'           => 'Completed',
                'external'           => 'W.I.P',
                'internal_color'     => '#DF00D0',
                'external_color'     => '#EFD000',
            ],
            [
                'internal'           => 'Redo',
                'external'           => 'W.I.P',
                'internal_color'     => '#FF4A93',
                'external_color'     => '#EFD000',

            ],
            [
                'internal'           => 'Close Internal',
                'external'           => 'W.I.P',
                'internal_color'     => '#58BA63',
                'external_color'     => '#EFD000',
                
            ],
            [
                'internal'           => 'Pending Owner Acceptance',
                'external'           => 'Pending Owner Acceptance',
                'internal_color'     => '#EF7215',
                'external_color'     => '#EF7215',
                
            ],
            [
                'internal'           => 'Redo Verification',
                'external'           => 'Pending Verification',
                'internal_color'     => '#FF4A93',
                'external_color'     => '#EFD000',
                
            ],
            [
                'internal'           => 'Pending Access',
                'external'           => 'Pending Access',
                'internal_color'     => '#F14915',
                'external_color'     => '#F14915',
            ],
            [
                'internal'           => 'Close External',
                'external'           => 'Close External',
                'internal_color'     => '#58BA63',
                'external_color'     => '#58BA63',
            ],
            [
                'internal'           => 'Decline',
                'external'           => 'Decline',
                'internal_color'     => '#FF3939',
                'external_color'     => '#FF3939',
            ],
        ])
            ->each(function ($status) {

                Status::create([
                    'internal'        => $status['internal'],
                    'external'        => $status['external'],
                    'internal_color'  => $status['internal_color'],
                    'external_color'  => $status['external_color'],
                ]);
            });
    }
}
