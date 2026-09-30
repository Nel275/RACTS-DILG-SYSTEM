<?php

namespace Database\Seeders;

use App\Models\ReportRequirement;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Default administrator account so you can log in for the first time.
        // Change this password after your first login, and definitely before
        // your defense demo.
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'DILG Administrator',
                'password' => 'password123',
                'role' => User::ROLE_ADMIN,
            ]
        );

        // A sample monitoring staff account, useful for testing role-based access.
        User::firstOrCreate(
            ['username' => 'staff'],
            [
                'name' => 'DILG Monitoring Staff',
                'password' => 'password123',
                'role' => User::ROLE_STAFF,
            ]
        );

        // The 18 canonical report slots from the client's redesign spec
        // (Section 1): 6 barangay-monthly, 5 barangay-yearly, 2
        // SK-monthly, 5 SK-yearly. "Annual Budget" exists in both the
        // barangay and SK yearly lists, which is why firstOrCreate below
        // matches on report_name + council_type together, not name alone.
        $reportRequirements = [
            // Barangay - monthly (6)
            ['report_name' => 'Kasambahay Report', 'council_type' => ReportRequirement::COUNCIL_BARANGAY, 'frequency' => ReportRequirement::FREQUENCY_MONTHLY, 'display_order' => 1],
            ['report_name' => 'Katarungang Pambarangay Report', 'council_type' => ReportRequirement::COUNCIL_BARANGAY, 'frequency' => ReportRequirement::FREQUENCY_MONTHLY, 'display_order' => 2],
            ['report_name' => 'Barangay Monthly Accomplishment Report', 'council_type' => ReportRequirement::COUNCIL_BARANGAY, 'frequency' => ReportRequirement::FREQUENCY_MONTHLY, 'display_order' => 3],
            ['report_name' => 'Road Clearing Operation Report', 'council_type' => ReportRequirement::COUNCIL_BARANGAY, 'frequency' => ReportRequirement::FREQUENCY_MONTHLY, 'display_order' => 4],
            ['report_name' => '1st Minutes of Meeting Report', 'council_type' => ReportRequirement::COUNCIL_BARANGAY, 'frequency' => ReportRequirement::FREQUENCY_MONTHLY, 'display_order' => 5],
            ['report_name' => '2nd Minutes of Meeting Report', 'council_type' => ReportRequirement::COUNCIL_BARANGAY, 'frequency' => ReportRequirement::FREQUENCY_MONTHLY, 'display_order' => 6],

            // Barangay - yearly (5)
            ['report_name' => 'SGBR', 'council_type' => ReportRequirement::COUNCIL_BARANGAY, 'frequency' => ReportRequirement::FREQUENCY_YEARLY, 'display_order' => 1],
            ['report_name' => '1st SBAD', 'council_type' => ReportRequirement::COUNCIL_BARANGAY, 'frequency' => ReportRequirement::FREQUENCY_YEARLY, 'display_order' => 2],
            ['report_name' => '2nd SBAD', 'council_type' => ReportRequirement::COUNCIL_BARANGAY, 'frequency' => ReportRequirement::FREQUENCY_YEARLY, 'display_order' => 3],
            ['report_name' => 'Annual Budget', 'council_type' => ReportRequirement::COUNCIL_BARANGAY, 'frequency' => ReportRequirement::FREQUENCY_YEARLY, 'display_order' => 4],
            ['report_name' => 'SGLGB', 'council_type' => ReportRequirement::COUNCIL_BARANGAY, 'frequency' => ReportRequirement::FREQUENCY_YEARLY, 'display_order' => 5],

            // SK - monthly (2)
            ['report_name' => 'Accomplishment Report', 'council_type' => ReportRequirement::COUNCIL_SK, 'frequency' => ReportRequirement::FREQUENCY_MONTHLY, 'display_order' => 1],
            ['report_name' => 'Minutes of Meeting Report', 'council_type' => ReportRequirement::COUNCIL_SK, 'frequency' => ReportRequirement::FREQUENCY_MONTHLY, 'display_order' => 2],

            // SK - yearly (5)
            ['report_name' => 'Annual Accomplishment Report', 'council_type' => ReportRequirement::COUNCIL_SK, 'frequency' => ReportRequirement::FREQUENCY_YEARLY, 'display_order' => 1],
            ['report_name' => 'Annual Budget', 'council_type' => ReportRequirement::COUNCIL_SK, 'frequency' => ReportRequirement::FREQUENCY_YEARLY, 'display_order' => 2],
            ['report_name' => 'IRP', 'council_type' => ReportRequirement::COUNCIL_SK, 'frequency' => ReportRequirement::FREQUENCY_YEARLY, 'display_order' => 3],
            ['report_name' => '1st KK Assembly', 'council_type' => ReportRequirement::COUNCIL_SK, 'frequency' => ReportRequirement::FREQUENCY_YEARLY, 'display_order' => 4],
            ['report_name' => '2nd KK Assembly', 'council_type' => ReportRequirement::COUNCIL_SK, 'frequency' => ReportRequirement::FREQUENCY_YEARLY, 'display_order' => 5],
        ];

        foreach ($reportRequirements as $reportRequirement) {
            ReportRequirement::firstOrCreate(
                [
                    'report_name' => $reportRequirement['report_name'],
                    'council_type' => $reportRequirement['council_type'],
                ],
                $reportRequirement + ['is_active' => true]
            );
        }
    }
}
