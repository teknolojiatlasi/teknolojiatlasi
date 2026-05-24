<?php

namespace Modules\Contact\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Contact\Models\ContactSetting;

class ContactDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ContactSetting::singleton();
    }
}
