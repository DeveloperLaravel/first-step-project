<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // تنظيف الكاش
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        /*
        |--------------------------------------------------------------------------
        | 1. تعريف Modules
        |--------------------------------------------------------------------------
        */
        $modules = [
            'users',
            'roles',
            'permissions',
            'departments',
            'doctors',
            'patients',
            'nurses',
            'appointments',
            'visits',
            'medical_records',
            'medicines',
            'prescriptions',
            'lab_tests',
            'radiologies',
            'rooms',
            'beds',
            'bed_assignments',
            'invoices',
            'invoice_items',
            'recharge_cards',
        ];

        /*
        |--------------------------------------------------------------------------
        | 2. تعريف Actions
        |--------------------------------------------------------------------------
        */
        $actions = ['view', 'create', 'update', 'delete','restore','forceDelete','export','import','archive','unarchive','approve','reject','assign','unassign','publish','unpublish','activate'];

        /*
        |--------------------------------------------------------------------------
        | 3. إنشاء كل الصلاحيات
        |--------------------------------------------------------------------------
        */
        foreach ($modules as $module) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'name' => "$module.$action",
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 4. إنشاء الأدوار
        |--------------------------------------------------------------------------
        */
        $roles = [
            'admin',
            'superviser',
            'doctor',
            'reception',
            'nurse',
            'pharmacist',
            'lab',
            'radiology',
            'accountant',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        /*
        |--------------------------------------------------------------------------
        | 5. إعطاء الصلاحيات
        |--------------------------------------------------------------------------
        */

        // 🔥 super-admin (كل شيء)
        $superAdmin = Role::findByName('admin');
        $superAdmin->syncPermissions(Permission::all());

        // 🔥 admin
        $admin = Role::findByName('admin');
        $admin->syncPermissions(Permission::all());

        // 👨‍⚕️ doctor
        $doctor = Role::findByName('superviser');
        $doctor->syncPermissions([
            'patients.view',
            'appointments.view',
            'visits.view',
            'visits.update',
            'medical_records.view',
            'medical_records.create',
            'medical_records.update',
            'prescriptions.create',
            'prescriptions.view',
            'lab_tests.view',
            'radiologies.view',
        ]);

        // 🧑‍💼 reception
        $reception = Role::findByName('reception');
        $reception->syncPermissions([
            'patients.view',
            'patients.create',
            'patients.update',

            'appointments.view',
            'appointments.create',
            'appointments.update',

            'visits.create',
            'visits.view',

            'bed_assignments.create',
            'bed_assignments.view',
        ]);

        // 👩‍⚕️ nurse
        $nurse = Role::findByName('nurse');
        $nurse->syncPermissions([
            'patients.view',
            'visits.view',
            'bed_assignments.view',
            'bed_assignments.update',
        ]);

        // 💊 pharmacist
        $pharmacist = Role::findByName('pharmacist');
        $pharmacist->syncPermissions([
            'medicines.view',
            'medicines.create',
            'medicines.update',

            'prescriptions.view',
        ]);

        // 🧪 lab
        $lab = Role::findByName('lab');
        $lab->syncPermissions([
            'lab_tests.view',
            'lab_tests.update',
        ]);

        // 🩻 radiology
        $radiology = Role::findByName('radiology');
        $radiology->syncPermissions([
            'radiologies.view',
            'radiologies.update',
        ]);

        // 💰 accountant
        $accountant = Role::findByName('accountant');
        $accountant->syncPermissions([
            'invoices.view',
            'invoices.create',
            'invoices.update',

            'invoice_items.view',
            'invoice_items.create',

            'recharge_cards.view',
            'recharge_cards.create',
        ]);
    }
}
