<?php
use Illuminate\Database\Seeder;
use App\Entity\Attribute;

class FormAttributeSeeder extends Seeder
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
                'display_name' => 'TextField (max 255 characters)',
                'name' => 'Text',
                'preset_value' => 'string|max:255',
                'allow_insert_value' => 0,
                'multiple_input' => 0,
                'multiple_row' => 1
            ],
            [
                'display_name' => 'Short Text (max 100 characters)',
                'name' => 'Text',
                'preset_value' => 'string|max:100',
                'allow_insert_value' => 0,
                'multiple_input' => 0,
                'multiple_row' => 1
            ],
            [
                'display_name' => 'Signature',
                'name' => 'Image',
                'preset_value' => 'image|mimes:jpg,png,jpeg',
                'allow_insert_value' => 0,
                'multiple_input' => 0,
                'multiple_row' => 0
            ],
            [
                'display_name' => 'Photo',
                'name' => 'Image',
                'preset_value' => 'image|mimes:jpg,png,jpeg',
                'allow_insert_value' => 0,
                'multiple_input' => 0,
                'multiple_row' => 0
            ],
            [
                'display_name' => 'Date',
                'name' => 'Date',
                'preset_value' => 'date',
                'allow_insert_value' => 0,
                'multiple_input' => 0,
                'multiple_row' => 0
            ],
            [
                'display_name' => 'Checkbox',
                'name' => 'Boolean',
                'preset_value' => 'in:0,1',
                'allow_insert_value' => 0,
                'multiple_input' => 1,
                'multiple_row' => 0
            ],
            [
                'display_name' => 'Choice',
                'name' => 'Boolean',
                'preset_value' => 'in:0,1',
                'allow_insert_value' => 0,
                'multiple_input' => 1,
                'multiple_row' => 0
            ],
            [
                'display_name' => 'Checkboxes (multiple selection)',
                'name' => 'Boolean',
                'preset_value' => 'in:0,1',
                'allow_insert_value' => 0,
                'multiple_input' => 1,
                'multiple_row' => 0
            ],
            [
                'display_name' => 'DropDownBox',
                'name' => 'Option',
                'preset_value' => 'in:',
                'allow_insert_value' => 1,
                'multiple_input' => 0,
                'multiple_row' => 0
            ],
        ])->each(function ($data) {
            
            Attribute::create([
                'name' => $data['name'],
                'display_name' => $data['display_name'],
                'preset_value' => $data['preset_value'],
                'allow_insert_value' => $data['allow_insert_value'],
                'multiple_input' => $data['multiple_input'],
                'multiple_row' => $data['multiple_row'],
            ]);
        });
    }
}
