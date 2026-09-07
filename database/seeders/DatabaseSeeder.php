<?php

namespace Database\Seeders;

use App\Models\LabTest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create(['name' => 'Lab Administrator', 'email' => 'admin@medilab.pk', 'password' => bcrypt('password')]);
        $tests = [
            ['CBC', 'Complete Blood Count', 'Hematology', 1200, 'EDTA Blood', '4 hours'], ['LFT', 'Liver Function Test', 'Biochemistry', 2200, 'Serum', '6 hours'], ['RFT', 'Renal Function Test', 'Biochemistry', 1800, 'Serum', '6 hours'], ['HbA1c', 'Glycated Hemoglobin', 'Diabetes', 1900, 'EDTA Blood', 'Same day'], ['TSH', 'Thyroid Stimulating Hormone', 'Hormones', 1800, 'Serum', 'Same day'], ['LIPID', 'Lipid Profile', 'Biochemistry', 2000, 'Serum', 'Same day'], ['URINE', 'Urine Complete Examination', 'Clinical Pathology', 700, 'Urine', '3 hours'], ['HBsAg', 'Hepatitis B Surface Antigen', 'Serology', 1500, 'Serum', 'Same day'], ['PCR-C', 'HCV PCR Quantitative', 'Molecular', 8500, 'EDTA Blood', '2 days'], ['VIT-D', 'Vitamin D (25-OH)', 'Vitamins', 3200, 'Serum', 'Same day'],
        ];
        foreach ($tests as $t) {
            LabTest::create(['code' => $t[0], 'name' => $t[1], 'category' => $t[2], 'price' => $t[3], 'sample_type' => $t[4], 'turnaround' => $t[5]]);
        }
        $patients = [['Muhammad Usman', '0300-1234567', 'Male', 34, 'Multan', 'Dr. Ahmed Raza'], ['Ayesha Siddiqua', '0312-9876543', 'Female', 28, 'Multan', 'Dr. Fatima Khan'], ['Ali Hassan', '0333-4567890', 'Male', 51, 'Shujabad', 'Walk-in'], ['Zainab Bibi', '0301-1122334', 'Female', 42, 'Khanewal', 'Dr. Sana Iqbal'], ['Hamza Tariq', '0321-7788990', 'Male', 19, 'Multan', 'Walk-in']];
        foreach ($patients as $i => $p) {
            Patient::create(['patient_no' => 'MLT-'.str_pad((string) ($i + 1), 5, '0', STR_PAD_LEFT), 'name' => $p[0], 'phone' => $p[1], 'gender' => $p[2], 'age' => $p[3], 'city' => $p[4], 'referred_by' => $p[5]]);
        }
        foreach ([[1, [1, 2], 'completed', 3400, 3400], [2, [4, 5], 'processing', 3700, 2000], [3, [1, 7], 'sample_collected', 1900, 1900], [4, [3, 6], 'pending', 3800, 0], [5, [1, 10], 'completed', 4400, 4400]] as $i => $o) {
            $order = Order::create(['invoice_no' => 'INV-260907-'.str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT), 'patient_id' => $o[0], 'status' => $o[2], 'total' => $o[3], 'paid' => $o[4], 'payment_method' => $i % 2 ? 'JazzCash' : 'Cash', 'reported_at' => $o[2] === 'completed' ? now() : null]);
            foreach ($o[1] as $testId) {
                $test = LabTest::find($testId);
                OrderItem::create(['order_id' => $order->id, 'lab_test_id' => $testId, 'price' => $test->price, 'result' => $o[2] === 'completed' ? 'Within normal limits' : null, 'reference_range' => 'As per age/gender']);
            }
        }
    }
}
